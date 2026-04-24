<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Membres - Agora Coopérative</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10px;
            color: #333;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 3px solid #4472C4;
        }

        .header h1 {
            color: #4472C4;
            font-size: 20px;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .header .subtitle {
            font-size: 11px;
            color: #666;
            margin-top: 5px;
        }

        .info-box {
            background-color: #f8f9fa;
            padding: 10px;
            margin-bottom: 15px;
            border-left: 4px solid #4472C4;
        }

        .info-box p {
            margin: 3px 0;
            font-size: 10px;
        }

        .info-box strong {
            color: #4472C4;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        thead {
            background-color: #4472C4;
            color: white;
        }

        thead th {
            padding: 8px 5px;
            text-align: left;
            font-size: 9px;
            font-weight: bold;
            border: 1px solid #2c5aa0;
        }

        tbody td {
            padding: 6px 5px;
            border: 1px solid #ddd;
            font-size: 9px;
        }

        tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        tbody tr:hover {
            background-color: #e9ecef;
        }

        .status-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
        }

        .status-actif {
            background-color: #d4edda;
            color: #155724;
        }

        .status-inactif {
            background-color: #f8d7da;
            color: #721c24;
        }

        .role-admin {
            color: #856404;
            font-weight: bold;
        }

        .role-membre {
            color: #004085;
        }

        .stats-section {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
            border: 1px solid #dee2e6;
        }

        .stats-section h3 {
            color: #4472C4;
            font-size: 12px;
            margin-bottom: 10px;
            border-bottom: 2px solid #4472C4;
            padding-bottom: 5px;
        }

        .stats-grid {
            display: table;
            width: 100%;
        }

        .stat-item {
            display: table-cell;
            width: 25%;
            padding: 8px;
            text-align: center;
        }

        .stat-item .label {
            font-size: 9px;
            color: #666;
            margin-bottom: 3px;
        }

        .stat-item .value {
            font-size: 16px;
            font-weight: bold;
            color: #4472C4;
        }

        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 2px solid #dee2e6;
            text-align: center;
            font-size: 8px;
            color: #666;
        }

        .page-break {
            page-break-after: always;
        }

        @page {
            margin: 15mm;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Liste des Membres</h1>
        <div class="subtitle">Agora Coopérative</div>
    </div>

    <div class="info-box">
        <p><strong>Date de génération :</strong> {{ now()->format('d/m/Y à H:i') }}</p>
        <p><strong>Nombre total de membres :</strong> {{ $membres->count() }}</p>
        <p><strong>Généré par :</strong> {{ auth()->user()->prenom }} {{ auth()->user()->nom }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 10%;">Code</th>
                <th style="width: 15%;">Nom</th>
                <th style="width: 15%;">Prénom</th>
                <th style="width: 20%;">Email</th>
                <th style="width: 12%;">Téléphone</th>
                <th style="width: 10%;">Rôle</th>
                <th style="width: 8%;">Statut</th>
                <th style="width: 10%;">Inscription</th>
            </tr>
        </thead>
        <tbody>
            @foreach($membres as $membre)
            <tr>
                <td>{{ $membre->code_membre }}</td>
                <td>{{ $membre->nom }}</td>
                <td>{{ $membre->prenom }}</td>
                <td style="font-size: 8px;">{{ $membre->email }}</td>
                <td>{{ $membre->telephone ?? 'N/A' }}</td>
                <td class="{{ $membre->role === 'administrateur' ? 'role-admin' : 'role-membre' }}">
                    {{ ucfirst($membre->role) }}
                </td>
                <td>
                    <span class="status-badge {{ $membre->est_actif ? 'status-actif' : 'status-inactif' }}">
                        {{ $membre->est_actif ? 'Actif' : 'Inactif' }}
                    </span>
                </td>
                <td>{{ $membre->date_inscription ? $membre->date_inscription->format('d/m/Y') : 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="stats-section">
        <h3>Statistiques</h3>
        <div class="stats-grid">
            <div class="stat-item">
                <div class="label">Total Membres</div>
                <div class="value">{{ $membres->count() }}</div>
            </div>
            <div class="stat-item">
                <div class="label">Actifs</div>
                <div class="value">{{ $membres->where('est_actif', true)->count() }}</div>
            </div>
            <div class="stat-item">
                <div class="label">Inactifs</div>
                <div class="value">{{ $membres->where('est_actif', false)->count() }}</div>
            </div>
            <div class="stat-item">
                <div class="label">Administrateurs</div>
                <div class="value">{{ $membres->where('role', 'administrateur')->count() }}</div>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>Document confidentiel - Agora Coopérative © {{ date('Y') }}</p>
        <p>Généré automatiquement le {{ now()->format('d/m/Y à H:i:s') }}</p>
    </div>
</body>
</html>
