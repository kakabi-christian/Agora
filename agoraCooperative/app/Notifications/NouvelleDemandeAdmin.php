<?php

namespace App\Notifications;

use App\Models\DemandeAdhesion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NouvelleDemandeAdmin extends Notification implements ShouldQueue
{
    use Queueable;

    public $demande;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(DemandeAdhesion $demande)
    {
        $this->demande = $demande;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Nouvelle demande d\'adhésion - Agora Coopérative')
                    ->greeting('Bonjour ' . $notifiable->prenom . ',')
                    ->line('Une nouvelle demande d\'adhésion a été soumise.')
                    ->line('**Demandeur:** ' . $this->demande->prenom . ' ' . $this->demande->nom)
                    ->line('**Email:** ' . $this->demande->email)
                    ->line('**Téléphone:** ' . $this->demande->telephone)
                    ->line('**Date de demande:** ' . $this->demande->date_demande->format('d/m/Y à H:i'))
                    ->action('Voir la demande', url('/api/admin/demandes-adhesion/' . $this->demande->id))
                    ->line('Merci de traiter cette demande dans les meilleurs délais.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'demande_id' => $this->demande->id,
            'demandeur_nom' => $this->demande->nom,
            'demandeur_prenom' => $this->demande->prenom,
            'demandeur_email' => $this->demande->email,
            'date_demande' => $this->demande->date_demande,
        ];
    }
}
