<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        //$this->call(UserSeeder::class);
        //$this->call(VoteSeeder::class);
        $this->call(TaxonomySeeder::class);
        $this->call(TermSeeder::class);
        $this->call(TaxonomyTermSeeder::class);

        $users = factory(App\User::class, 120)->create();
        $projects = factory(App\Project::class, 5)->create();
        factory(App\PublicConsultation::class, 10)->create();

        foreach($projects as $key => $project) {
            for ($i = 0; $i <= 3; $i++) {
                $project->articles()
                    ->create(
                        factory(App\Article::class)
                            ->make(['project_id' => $key ])
                            ->toArray());
                $project->ideas()
                    ->create(
                        factory(App\Idea::class)
                            ->make(['project_id' => $key ])
                            ->toArray());
            }
        }

        foreach($users as $key =>$user) {
            $user->comments()
                ->create(
                    factory(App\Comment::class)
                        ->make(['user_id' => $key,
                            'project_id' => App\Project::all()->random()->id ])
                        ->toArray());
            $user->comments()
                ->create(
                    factory(App\Comment::class)
                        ->make(['user_id' => $key,
                            'article_id' => App\Article::all()->random()->id ])
                        ->toArray());
            $user->comments()
                ->create(
                    factory(App\Comment::class)
                        ->make(['user_id' => $key,
                            'idea_id' => App\Idea::all()->random()->id ])
                        ->toArray());
            $user->comments()
                ->create(
                    factory(App\Comment::class)
                        ->make(['user_id' => $key,
                            'public_consultation_id' => App\PublicConsultation::all()->random()->id ])
                        ->toArray());

            $user->votes()
                ->create(
                    factory(App\Vote::class)
                        ->make(['user_id' => $key,
                            'project_id' => App\Project::all()->random()->id,
                            'vote' => rand(0,2)])->toArray());
            $user->votes()
                ->create(
                    factory(App\Vote::class)
                        ->make(['user_id' => $key,
                            'article_id' => App\Article::all()->random()->id,
                            'vote' => rand(0,2)])->toArray());
            $user->votes()
                ->create(
                    factory(App\Vote::class)
                        ->make(['user_id' => $key,
                            'idea_id' => App\Idea::all()->random()->id,
                            'vote' => rand(0,2)])->toArray());
            $user->votes()
                ->create(
                    factory(App\Vote::class)
                        ->make(['user_id' => $key,
                            'public_consultation_id' => App\PublicConsultation::all()->random()->id,
                            'vote' => rand(0,1)])->toArray());
            $user->votes()
                ->create(
                    factory(App\Vote::class)
                        ->make(['user_id' => $key,
                            'comment_id' => App\Comment::all()->random()->id,
                            'vote' => rand(0,1)])->toArray());
        }
        $this->call(ProjectTermSeeder::class);
        $this->call(PublicConsultationTermSeeder::class);
    }
}
