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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', Rules\Password::defaults()],
            'password_confirmation' => ['required', 'same:password'],
            'user_plan' => ['required', 'string', 'in:basic,premium,elite'],
        ]);

        $user = User::create([
            'name' => ucwords($request->name),
            'email' => $request->email,
            'password' => Hash::make($request->string('password')),
            'user_plan' => 'premium',
            'user_subscription_status' => 'trialing',
            'trial_ends_at' => Carbon::now()->addDays(30),
            'email_verified_at' => '2000-01-01 00:00:00',
        ]);

        event(new Registered($user));

        Auth::login($user);

        // Création du salon
        $createSalonUsecase = app(CreateSalonUsecase::class);
        $salon = $createSalonUsecase->execute([
            'name' => ucwords($user->name),
            'name_slug' => Str::slug($user->name),
            'owner_id' => $user->id
        ]);

        // Envoyer le mail de bienvenue
        // Mail::to($user->email)->send(new WelcomeTrialStarted($user));

        // Envoyer une notification Discord
        // DiscordNotification::send('inscriptions', "Nouvel utilisateur inscrit : {$user->name} ({$user->email})");

        // Enregistrer l'envoi du mail
        // EmailNotificationModel::create([
        //     'user_id' => $user->id,
        //     'type' => 'welcome_trial_started',
        //     'sent_at' => Carbon::now()
        // ]);

        return response()->noContent();
    }
}
