<?php

namespace App\Http\Controllers\Auth;

use App\Application\Usecases\Salon\CreateSalonUsecase;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): Response
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', Rules\Password::defaults()],
            'password_confirmation' => ['required', 'same:password'],
            'salon_name' => ['required', 'string', 'max:255'],
            'user_plan' => ['nullable', 'string', 'in:basic,premium'],
        ]);

        $user = User::create([
            'firstName' => ucwords($request->first_name),
            'lastName' => ucwords($request->last_name),
            'email' => $request->email,
            'password' => Hash::make($request->string('password')),
            'role' => 'salon_owner',
            'user_plan' => $request->user_plan ?? 'basic',
            'user_subscription_status' => 'trialing',
            'trial_ends_at' => Carbon::now()->addDays(30),
            'email_verified_at' => '2000-01-01 00:00:00',
        ]);

        event(new Registered($user));

        Auth::login($user);

        // Création du salon
        $createSalonUsecase = app(CreateSalonUsecase::class);
        $salon = $createSalonUsecase->execute([
            'name' => ucwords($request->salon_name),
            'name_slug' => Str::slug($request->salon_name),
            'owner_id' => $user->id
        ]);

        // Mise à jour de l'utilisateur avec l'ID du salon
        $user->update(['salon_id' => $salon->getId()]);

        // Envoyer le mail de bienvenue
        // Mail::to($user->email)->send(new WelcomeTrialStarted($user));

        // Envoyer une notification Discord
        // DiscordNotification::send('inscriptions', "Nouvel utilisateur inscrit : {$user->firstName} {$user->lastName} ({$user->email})");

        // Enregistrer l'envoi du mail
        // EmailNotificationModel::create([
        //     'user_id' => $user->id,
        //     'type' => 'welcome_trial_started',
        //     'sent_at' => Carbon::now()
        // ]);

        return response()->noContent();
    }
}
