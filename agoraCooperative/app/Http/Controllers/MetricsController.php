<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Membre;
use App\Models\Evenements;
use App\Models\Projets;
use App\Models\Don;

class MetricsController extends Controller
{
    public function index()
    {
        $metrics = [];
        
        try {
            // Métriques de base de l'application
            $metrics[] = '# HELP app_membres_total Nombre total de membres';
            $metrics[] = '# TYPE app_membres_total gauge';
            $metrics[] = 'app_membres_total ' . Membre::count();
            
            $metrics[] = '# HELP app_membres_actifs Nombre de membres actifs';
            $metrics[] = '# TYPE app_membres_actifs gauge';
            $metrics[] = 'app_membres_actifs ' . Membre::where('est_actif', 1)->count();
            
            $metrics[] = '# HELP app_evenements_total Nombre total d\'événements';
            $metrics[] = '# TYPE app_evenements_total gauge';
            $metrics[] = 'app_evenements_total ' . Evenements::count();
            
            $metrics[] = '# HELP app_evenements_a_venir Nombre d\'événements à venir';
            $metrics[] = '# TYPE app_evenements_a_venir gauge';
            $metrics[] = 'app_evenements_a_venir ' . Evenements::where('date_debut', '>', now())->count();
            
            $metrics[] = '# HELP app_projets_total Nombre total de projets';
            $metrics[] = '# TYPE app_projets_total gauge';
            $metrics[] = 'app_projets_total ' . Projets::count();
            
            $metrics[] = '# HELP app_projets_en_cours Nombre de projets en cours';
            $metrics[] = '# TYPE app_projets_en_cours gauge';
            $metrics[] = 'app_projets_en_cours ' . Projets::where('statut', 'en_cours')->count();
            
            $metrics[] = '# HELP app_dons_total_montant Montant total des dons';
            $metrics[] = '# TYPE app_dons_total_montant gauge';
            $metrics[] = 'app_dons_total_montant ' . (Don::sum('montant') ?? 0);
            
            // Métriques MySQL détaillées
            $metrics[] = '# HELP mysql_threads_connected Nombre de connexions MySQL actives';
            $metrics[] = '# TYPE mysql_threads_connected gauge';
            $dbConnections = DB::select('SHOW STATUS LIKE "Threads_connected"');
            $metrics[] = 'mysql_threads_connected ' . ($dbConnections[0]->Value ?? 0);
            
            $metrics[] = '# HELP mysql_threads_running Nombre de threads MySQL en cours d\'exécution';
            $metrics[] = '# TYPE mysql_threads_running gauge';
            $threadsRunning = DB::select('SHOW STATUS LIKE "Threads_running"');
            $metrics[] = 'mysql_threads_running ' . ($threadsRunning[0]->Value ?? 0);
            
            $metrics[] = '# HELP mysql_queries_total Nombre total de requêtes exécutées';
            $metrics[] = '# TYPE mysql_queries_total counter';
            $queries = DB::select('SHOW STATUS LIKE "Questions"');
            $metrics[] = 'mysql_queries_total ' . ($queries[0]->Value ?? 0);
            
            $metrics[] = '# HELP mysql_slow_queries_total Nombre de requêtes lentes';
            $metrics[] = '# TYPE mysql_slow_queries_total counter';
            $slowQueries = DB::select('SHOW STATUS LIKE "Slow_queries"');
            $metrics[] = 'mysql_slow_queries_total ' . ($slowQueries[0]->Value ?? 0);
            
            $metrics[] = '# HELP mysql_uptime_seconds Temps de fonctionnement MySQL en secondes';
            $metrics[] = '# TYPE mysql_uptime_seconds counter';
            $uptime = DB::select('SHOW STATUS LIKE "Uptime"');
            $metrics[] = 'mysql_uptime_seconds ' . ($uptime[0]->Value ?? 0);
            
            $metrics[] = '# HELP mysql_max_connections Nombre maximum de connexions configurées';
            $metrics[] = '# TYPE mysql_max_connections gauge';
            $maxConn = DB::select('SHOW VARIABLES LIKE "max_connections"');
            $metrics[] = 'mysql_max_connections ' . ($maxConn[0]->Value ?? 0);
            
            $metrics[] = '# HELP mysql_table_locks_waited Nombre de verrous de table en attente';
            $metrics[] = '# TYPE mysql_table_locks_waited counter';
            $tableLocks = DB::select('SHOW STATUS LIKE "Table_locks_waited"');
            $metrics[] = 'mysql_table_locks_waited ' . ($tableLocks[0]->Value ?? 0);
            
            $metrics[] = '# HELP mysql_bytes_received_total Octets reçus par MySQL';
            $metrics[] = '# TYPE mysql_bytes_received_total counter';
            $bytesReceived = DB::select('SHOW STATUS LIKE "Bytes_received"');
            $metrics[] = 'mysql_bytes_received_total ' . ($bytesReceived[0]->Value ?? 0);
            
            $metrics[] = '# HELP mysql_bytes_sent_total Octets envoyés par MySQL';
            $metrics[] = '# TYPE mysql_bytes_sent_total counter';
            $bytesSent = DB::select('SHOW STATUS LIKE "Bytes_sent"');
            $metrics[] = 'mysql_bytes_sent_total ' . ($bytesSent[0]->Value ?? 0);
            
            // Métriques de taille de base de données
            $metrics[] = '# HELP mysql_database_size_bytes Taille de la base de données en octets';
            $metrics[] = '# TYPE mysql_database_size_bytes gauge';
            $dbSize = DB::select("
                SELECT SUM(data_length + index_length) as size 
                FROM information_schema.TABLES 
                WHERE table_schema = DATABASE()
            ");
            $metrics[] = 'mysql_database_size_bytes ' . ($dbSize[0]->size ?? 0);
            
        } catch (\Exception $e) {
            $metrics[] = '# ERROR: ' . $e->getMessage();
        }
        
        return response(implode("\n", $metrics), 200)
            ->header('Content-Type', 'text/plain; version=0.0.4');
    }
}
