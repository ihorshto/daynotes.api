<?php

declare(strict_types=1);

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;

describe('Update User Lang', function (): void {
    beforeEach(function (): void {
        $this->user = User::factory()->create();
    });

    it('successfully updates lang to english', function (): void {
        Sanctum::actingAs($this->user);

        $response = $this->patchJson(route('user.updateLang'), ['lang' => 'en']);

        $response->assertSuccessful()
            ->assertJson(['lang' => 'en']);

        $this->assertDatabaseHas('users', [
            'id'   => $this->user->id,
            'lang' => 'en',
        ]);
    });

    it('successfully updates lang to ukrainian', function (): void {
        Sanctum::actingAs($this->user);

        $response = $this->patchJson(route('user.updateLang'), ['lang' => 'uk']);

        $response->assertSuccessful()
            ->assertJson(['lang' => 'uk']);

        $this->assertDatabaseHas('users', [
            'id'   => $this->user->id,
            'lang' => 'uk',
        ]);
    });

    it('successfully updates lang to french', function (): void {
        Sanctum::actingAs($this->user);

        $response = $this->patchJson(route('user.updateLang'), ['lang' => 'fr']);

        $response->assertSuccessful()
            ->assertJson(['lang' => 'fr']);

        $this->assertDatabaseHas('users', [
            'id'   => $this->user->id,
            'lang' => 'fr',
        ]);
    });

    it('fails with 422 when lang is missing', function (): void {
        Sanctum::actingAs($this->user);

        $response = $this->patchJson(route('user.updateLang'), []);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['lang']);
    });

    it('fails with 422 when lang value is invalid', function (): void {
        Sanctum::actingAs($this->user);

        $response = $this->patchJson(route('user.updateLang'), ['lang' => 'de']);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['lang']);
    });

    it('fails with 401 when unauthenticated', function (): void {
        $response = $this->patchJson(route('user.updateLang'), ['lang' => 'en']);

        $response->assertUnauthorized();
    });
});
