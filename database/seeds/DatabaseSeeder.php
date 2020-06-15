<?php

use App\User;
use App\Client;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // create client user
        $user = User::create([
            'name' => 'fahd client',
            'email' => 'fahd@client.com',
            'password' => Hash::make('123456789')
        ]);
        
        // create simple user
        User::create([
            'name' => 'fahd user',
            'email' => 'fahd@user.com',
            'password' => Hash::make('123456789')
        ]);
        
        // Create Client 
        Client::create([
            'user_id'=> $user->id,
            'name' => 'Fahd Corp',
            'description' => `{
                "type": "doc",
                "content": [
                  {
                    "type": "heading",
                    "attrs": {
                      "level": 2
                    },
                    "content": [
                      {
                        "type": "text",
                        "text": "About us"
                      }
                    ]
                  },
                  {
                    "type": "paragraph",
                    "content": [
                      {
                        "type": "text",
                        "text": " Nous sommes des architectes de la transformation des entreprises et de la modernisation des Etats, courageux, authentiques, ouverts, engagés et élégants. L'organisation de nos expertises en communautés ouvertes, permet d'apporter à nos clients une proposition de valeur depuis la réflexion stratégique jusqu’à sa mise en œuvre en intégrant les compétences métiers et tech les plus avancées.Nous sommes aujourd'hui 2300 collaborateurs, répartis dans 15 implantations dans le monde (Paris, Bordeaux, Toulouse, Nantes, Lyon, Amsterdam, New-York, Bruxelles,Luxembourg, Melbourne, Singapour, Montréal, Tunis, Zele).Mission : Nous aidons chacun de nos clients à dessiner concrètement un chemin d’avenir en étant audacieux, en allant au-delà de l’évidence, pour créer de nouvelles façons de travailler, de nouveaux modèles économiques et de nouveaux lieux. Autrement dit, chaque matin, nous nous levons pour contribuer à dessiner un nouveau monde.C’est ainsi qu'est définie notre raison d’être – Design a New World – et notre signature : Beyond the Obvious, au-delà de l’évidence."
                      }
                    ]
                  }
                ]
              }`,
            'slug' => 'fahd_corp'
        ]);
    }
}