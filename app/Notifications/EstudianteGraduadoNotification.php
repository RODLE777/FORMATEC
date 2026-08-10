<?php

namespace App\Notifications;

use App\Models\Inscripcion;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Se envia cuando una inscripcion pasa a GRADUADO, ya sea automatico
 * (trigger al completar evaluaciones) o manual (staff cambia el
 * estado). Como el trigger corre en la base de datos, el "disparo" se
 * hace desde PHP comparando el resultado_final antes/despues de
 * guardar (ver EvaluacionController e InscripcionController).
 */
class EstudianteGraduadoNotification extends Notification
{
    use Queueable;

    public function __construct(private Inscripcion $inscripcion) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $estudiante = $this->inscripcion->estudiante;
        $curso = $this->inscripcion->grupo->curso->nombre;

        return (new MailMessage)
            ->subject('¡Felicidades! Has finalizado '.$curso)
            ->greeting('Hola '.$estudiante->nombres.',')
            ->line("Has finalizado exitosamente el curso de {$curso} con FORMATEC.")
            ->line('Nota final: '.$this->inscripcion->nota_final)
            ->line('Tu constancia estara disponible pronto en nuestras oficinas.')
            ->line('¡Gracias por tu esfuerzo!');
    }
}
