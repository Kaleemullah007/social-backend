<?php

namespace Tests\Feature\Api;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * A basic feature test example.
     */
    public function test_a_basic_request(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /**
     * create post by logged in post.
     */

    public function test_logged_in_user_can_create_post()
    {
        $user = User::factory()->create();
        $token = $user->createToken('API Token')->plainTextToken;

        $postData = [
            'title' => $this->faker->sentence,
            'body' => $this->faker->paragraph,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/posts', $postData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('posts', $postData);
    }

    /**
     * get post by logged in post.
     */

    public function test_logged_in_user_can_get_posts()
    {
        $user = User::factory()->create();
        $token = $user->createToken('API Token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/posts');

        $response->assertStatus(200);
    }


    public function test_logged_in_user_can_like_posts()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();
        $token = $user->createToken('API Token')->plainTextToken;
        $postData = [
            'post_id' => $post->id,
        ];
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->post('/api/posts/like', $postData);

        $response->assertStatus(200);
    }


    // Update post
    public function test_logged_in_user_can_edit_post()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $postData = [
            'title' => $this->faker->sentence,
            'body' => $this->faker->paragraph,
        ];

        $token = $user->createToken('API Token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->put('/api/posts/' . $post['id'], $postData);

        $post->refresh();
        $this->assertEquals($post->title, $postData['title']);
    }

    // Delete post

    public function test_logged_in_user_can_delete_post()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $postData = [
            'title' => $this->faker->sentence,
            'body' => $this->faker->paragraph,
        ];

        $token = $user->createToken('API Token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->delete('/api/posts/' . $post['id']);


        $response->assertStatus(204);
    }


    // Validation post 
    public function test_logged_in_user_can_validate_post_body()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $postData = [
            'title' => $this->faker->sentence,
            'body' => '',
        ];

        $token = $user->createToken('API Token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->put('/api/posts/' . $post['id'], $postData);

        $response->assertStatus(422);
    }


    // Validation post 
    public function test_logged_in_user_can_validate_post_body_create_post()
    {
        $user = User::factory()->create();

        $postData = [
            'title' => $this->faker->sentence,
            'body' => '',
        ];

        $token = $user->createToken('API Token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->post('/api/posts', $postData);

        $response->assertStatus(422);
    }

        // Validation post 
        public function test_logged_in_user_can_search_post()
        {
            $user = User::factory()->create();
    
            $postData = [
                'title' => $this->faker->name,
                'body' => 'Hel',
                'is_public'=>true,
                
            ];
            Post::factory()->create($postData);

    
            $token = $user->createToken('API Token')->plainTextToken;
    
            $response = $this->withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json'
            ])->get('/api/posts/search?search=He');
    
            $response->assertStatus(200);
        }
}
