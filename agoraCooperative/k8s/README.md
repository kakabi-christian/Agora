# Déploiement Kubernetes - Agora Cooperative

## 📋 Prérequis

- Kubernetes cluster (v1.24+)
- kubectl configuré
- Metrics Server installé (pour HPA)
- Ingress Controller (nginx recommandé)
- Helm (optionnel)

## 🏗️ Architecture

```
┌─────────────────────────────────────────────────────────┐
│                    Ingress (nginx)                       │
│  agora.example.com | grafana.agora.example.com          │
└────────────────────┬────────────────────────────────────┘
                     │
        ┌────────────┼────────────┐
        │            │            │
   ┌────▼────┐  ┌───▼────┐  ┌───▼────┐
   │  App    │  │Grafana │  │Prometheus│
   │ Service │  │Service │  │ Service  │
   └────┬────┘  └────────┘  └──────────┘
        │
   ┌────▼──────────────────┐
   │  App Pods (3-10)      │
   │  - PHP-FPM            │
   │  - Nginx              │
   │  Auto-scaling (HPA)   │
   └────┬──────────────────┘
        │
   ┌────▼────┐
   │  MySQL  │
   │  Pod    │
   └─────────┘
```

## 🚀 Déploiement Rapide

### 1. Configuration Initiale

```bash
cd k8s/

# Modifier les secrets
nano 02-secrets.yaml
# Remplacer APP_KEY, DB_PASSWORD, etc.

# Modifier l'image Docker
nano 05-app-deployment.yaml
# Remplacer YOUR_DOCKERHUB_USERNAME
```

### 2. Déploiement avec Rolling Update (Recommandé)

```bash
chmod +x deploy.sh
./deploy.sh rolling
```

### 3. Déploiement avec Blue-Green

```bash
./deploy.sh blue-green
```

## 📦 Déploiement Manuel

```bash
# 1. Namespace
kubectl apply -f 00-namespace.yaml

# 2. Configuration
kubectl apply -f 01-configmap.yaml
kubectl apply -f 02-secrets.yaml
kubectl apply -f 06-nginx-configmap.yaml

# 3. Volumes
kubectl apply -f 03-persistent-volumes.yaml

# 4. Base de données
kubectl apply -f 04-mysql-deployment.yaml

# 5. Application
kubectl apply -f 05-app-deployment.yaml

# 6. Auto-scaling
kubectl apply -f 07-hpa.yaml

# 7. Monitoring
kubectl apply -f 09-prometheus-deployment.yaml
kubectl apply -f 10-grafana-deployment.yaml

# 8. Ingress
kubectl apply -f 11-ingress.yaml
```

## 🔄 Stratégies de Déploiement

### Rolling Update (Par défaut)

Mise à jour progressive sans downtime :
- MaxSurge: 1 pod supplémentaire pendant la mise à jour
- MaxUnavailable: 1 pod peut être indisponible

```bash
# Mettre à jour l'image
kubectl set image deployment/agora-app \
  app=YOUR_USERNAME/agora-cooperative:v2.0.0 \
  -n agora-cooperative

# Suivre le déploiement
kubectl rollout status deployment/agora-app -n agora-cooperative
```

### Blue-Green Deployment

Deux environnements complets (Blue et Green) :

```bash
# 1. Déployer la nouvelle version (Green) avec 0 replicas
kubectl apply -f 08-blue-green-deployment.yaml

# 2. Scaler Green à 3 replicas
kubectl scale deployment agora-app-green --replicas=3 -n agora-cooperative

# 3. Attendre que Green soit prêt
kubectl wait --for=condition=ready pod -l version=green -n agora-cooperative

# 4. Basculer le trafic vers Green
./blue-green-switch.sh green

# 5. Vérifier que tout fonctionne
# Si OK, scaler Blue à 0
kubectl scale deployment agora-app-blue --replicas=0 -n agora-cooperative

# Si problème, revenir à Blue
./blue-green-switch.sh blue
```

## 📈 Auto-scaling (HPA)

L'HPA est configuré pour :
- **Min replicas** : 3
- **Max replicas** : 10
- **CPU target** : 70%
- **Memory target** : 80%

```bash
# Voir le statut HPA
kubectl get hpa -n agora-cooperative

# Détails
kubectl describe hpa agora-app-hpa -n agora-cooperative

# Tester l'auto-scaling (générer de la charge)
kubectl run -it --rm load-generator --image=busybox -n agora-cooperative \
  -- /bin/sh -c "while true; do wget -q -O- http://agora-app-service; done"
```

## 🔙 Rollback

```bash
# Rollback automatique
./rollback.sh

# Ou manuellement
kubectl rollout undo deployment/agora-app -n agora-cooperative

# Rollback vers une révision spécifique
kubectl rollout undo deployment/agora-app --to-revision=2 -n agora-cooperative

# Voir l'historique
kubectl rollout history deployment/agora-app -n agora-cooperative
```

## 🔍 Monitoring et Debugging

### Voir les logs

```bash
# Logs de l'application
kubectl logs -f -l app=agora-app -n agora-cooperative

# Logs d'un pod spécifique
kubectl logs -f POD_NAME -n agora-cooperative

# Logs du conteneur nginx
kubectl logs -f POD_NAME -c nginx -n agora-cooperative
```

### Statut des ressources

```bash
# Tous les pods
kubectl get pods -n agora-cooperative

# Déploiements
kubectl get deployments -n agora-cooperative

# Services
kubectl get services -n agora-cooperative

# HPA
kubectl get hpa -n agora-cooperative

# Ingress
kubectl get ingress -n agora-cooperative
```

### Exécuter des commandes

```bash
# Shell dans un pod
kubectl exec -it POD_NAME -n agora-cooperative -- /bin/bash

# Exécuter une commande artisan
kubectl exec POD_NAME -n agora-cooperative -- php artisan migrate

# Voir les variables d'environnement
kubectl exec POD_NAME -n agora-cooperative -- env
```

### Port-forwarding (accès local)

```bash
# Application
kubectl port-forward -n agora-cooperative svc/agora-app-service 8080:80

# Grafana
kubectl port-forward -n agora-cooperative svc/grafana-service 3000:3000

# Prometheus
kubectl port-forward -n agora-cooperative svc/prometheus-service 9090:9090

# MySQL
kubectl port-forward -n agora-cooperative svc/mysql-service 3306:3306
```

## 🔐 Sécurité

### Créer les secrets

```bash
# Générer APP_KEY
php artisan key:generate --show

# Créer le secret Docker Registry
kubectl create secret docker-registry docker-registry-secret \
  --docker-server=https://index.docker.io/v1/ \
  --docker-username=YOUR_USERNAME \
  --docker-password=YOUR_PASSWORD \
  --docker-email=YOUR_EMAIL \
  -n agora-cooperative
```

### TLS/SSL avec cert-manager

```bash
# Installer cert-manager
kubectl apply -f https://github.com/cert-manager/cert-manager/releases/download/v1.12.0/cert-manager.yaml

# Créer un ClusterIssuer
kubectl apply -f - <<EOF
apiVersion: cert-manager.io/v1
kind: ClusterIssuer
metadata:
  name: letsencrypt-prod
spec:
  acme:
    server: https://acme-v02.api.letsencrypt.org/directory
    email: your-email@example.com
    privateKeySecretRef:
      name: letsencrypt-prod
    solvers:
    - http01:
        ingress:
          class: nginx
EOF
```

## 🧹 Nettoyage

```bash
# Supprimer tout le namespace
kubectl delete namespace agora-cooperative

# Ou supprimer individuellement
kubectl delete -f 11-ingress.yaml
kubectl delete -f 10-grafana-deployment.yaml
kubectl delete -f 09-prometheus-deployment.yaml
kubectl delete -f 07-hpa.yaml
kubectl delete -f 05-app-deployment.yaml
kubectl delete -f 04-mysql-deployment.yaml
kubectl delete -f 03-persistent-volumes.yaml
```

## 📊 Métriques et Performances

### Ressources par pod

- **App (PHP-FPM)** : 256Mi-512Mi RAM, 200m-500m CPU
- **Nginx** : 64Mi-128Mi RAM, 50m-100m CPU
- **MySQL** : 512Mi-1Gi RAM, 250m-500m CPU
- **Prometheus** : 512Mi-1Gi RAM, 250m-500m CPU
- **Grafana** : 256Mi-512Mi RAM, 100m-200m CPU

### Capacité estimée

Avec 3 replicas minimum :
- ~1.5Gi RAM minimum
- ~1 CPU minimum
- Peut gérer ~1000 requêtes/minute

Avec 10 replicas maximum :
- ~5Gi RAM maximum
- ~5 CPU maximum
- Peut gérer ~5000 requêtes/minute

## 🆘 Troubleshooting

### Pods en CrashLoopBackOff

```bash
kubectl describe pod POD_NAME -n agora-cooperative
kubectl logs POD_NAME -n agora-cooperative --previous
```

### HPA ne scale pas

```bash
# Vérifier metrics-server
kubectl get apiservice v1beta1.metrics.k8s.io

# Installer metrics-server si nécessaire
kubectl apply -f https://github.com/kubernetes-sigs/metrics-server/releases/latest/download/components.yaml
```

### Ingress ne fonctionne pas

```bash
# Vérifier l'ingress controller
kubectl get pods -n ingress-nginx

# Installer nginx-ingress si nécessaire
helm install nginx-ingress ingress-nginx/ingress-nginx
```

## 📚 Ressources

- [Documentation Kubernetes](https://kubernetes.io/docs/)
- [HPA Documentation](https://kubernetes.io/docs/tasks/run-application/horizontal-pod-autoscale/)
- [Rolling Update](https://kubernetes.io/docs/tutorials/kubernetes-basics/update/update-intro/)
- [Blue-Green Deployment](https://kubernetes.io/blog/2018/04/30/zero-downtime-deployment-kubernetes-jenkins/)
