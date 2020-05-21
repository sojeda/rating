<?php

namespace Laraveles\Rating\Test;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Laraveles\Rating\Events\ModelRated;
use Laraveles\Rating\Events\ModelUnrated;
use Laraveles\Rating\Exception\InvalidScoreRating;
use Laraveles\Rating\Models\Rating;
use Laraveles\Rating\Test\Models\Page;
use Laraveles\Rating\Test\Models\SimplePage;
use Laraveles\Rating\Test\Models\User;

class RatingTest extends TestCase
{
    use RefreshDatabase;

    public function testNoImplements()
    {
        $this->expectException(\TypeError::class);

        /** @var User $user */
        $user = factory(User::class)->create();
        /** @var Page $page */
        $page = factory(SimplePage::class)->create();

        $user->rate($page, 5);
    }

    public function test_rate_product()
    {
        Event::fake();

        /** @var User $user */
        $user = factory(User::class)->create();
        /** @var Page $page */
        $page = factory(Page::class)->create();

        $result = $user->rate($page, 5);

        $this->assertIsBool($result);
        $this->assertTrue($result);
        $this->assertEquals(1, $page->qualifications()->count());

        Event::assertDispatched(ModelRated::class, function (ModelRated $event) use ($page) {
            return $event->getModel()->getKey() === $page->id;
        });
    }

    public function test_rate_product_has_rate()
    {
        Event::fake();

        /** @var User $user */
        $user = factory(User::class)->create();
        /** @var Page $page */
        $page = factory(Page::class)->create();

        $user->rate($page, 5);
        $user->rate($page, 4);
        $result = $user->rate($page, 5);

        $this->assertIsBool($result);
        $this->assertFalse($result);
        $this->assertEquals(1, $page->qualifications()->count());

        Event::assertDispatchedTimes(ModelRated::class, 1);
    }

    public function test_unrate_product()
    {
        Event::fake();

        /** @var User $user */
        $user = factory(User::class)->create();
        /** @var Page $page */
        $page = factory(Page::class)->create();

        $user->rate($page, 5);
        $result = $user->unrate($page);

        $this->assertIsBool($result);
        $this->assertTrue($result);

        Event::assertDispatched(ModelUnrated::class, function (ModelUnrated $event) use ($page) {
            return $event->getModel()->getKey() === $page->id;
        });
    }

    public function test_unrate_product_has_not_rate()
    {
        Event::fake();

        /** @var User $user */
        $user = factory(User::class)->create();
        /** @var Page $page */
        $page = factory(Page::class)->create();

        $result = $user->unrate($page);

        $this->assertIsBool($result);
        $this->assertFalse($result);
        $this->assertEquals(0, $page->qualifications()->count());

        Event::assertNotDispatched(ModelUnrated::class);
    }

    public function testUpdateRating()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        /** @var Page $page */
        $page = factory(Page::class)->create();

        $this->assertTrue($user->rate($page, 10.00));
        $this->assertEquals(10.00, $page->averageRating());

        $this->assertTrue($user->updateRatingFor($page, 1.00));
        $this->assertEquals(1.00, $page->averageRating());
    }

    public function test_product_doesnt_have_rates()
    {
        /** @var Page $product */
        $product = factory(Page::class)->create();

        $this->assertEquals(0.0, $product->averageRating());
    }

    public function test_average_rating()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        /** @var User $user2 */
        $user2 = factory(User::class)->create();
        /** @var Page $page */
        $page = factory(Page::class)->create();

        $user->rate($page, 5);
        $user2->rate($page, 3);

        $this->assertEquals(4, $page->averageRating());
    }

    public function test_rate_product_with_invalid_score()
    {
        $this->expectException(InvalidScoreRating::class);

        /** @var User $user */
        $user = factory(User::class)->create();
        /** @var Page $page */
        $page = factory(Page::class)->create();

        $user->rate($page, 15);
    }

    public function test_rate_other_model()
    {
        $page = factory(Page::class)->create();
        $user = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $user3 = factory(User::class)->create();


        $this->assertTrue($user->rate($page, 10.00));
        $this->assertFalse($user->rate($page, 10.00));
        $this->assertTrue($user->hasRated($page));

        $this->assertTrue($user2->rate($page, 7.00));
        $this->assertTrue($user3->rate($page, 5.00));

        $this->assertFalse($page->hasRated($user));
        $this->assertFalse($page->hasRated($user2));
        $this->assertFalse($page->hasRated($user3));

        $this->assertEquals(0, $page->ratings()->count());
        $this->assertEquals(0, $page->raters()->count());
        $this->assertEquals(0, $page->ratings(User::class)->count());
        $this->assertEquals(3, $page->raters(User::class)->count());

        $this->assertEquals(0, $user->ratings()->count());
        $this->assertEquals(0, $user->raters()->count());
        $this->assertEquals(1, $user->ratings(Page::class)->count());
        $this->assertEquals(0, $user->raters(Page::class)->count());

        $this->assertEquals(0, $user2->ratings()->count());
        $this->assertEquals(0, $user2->raters()->count());
        $this->assertEquals(1, $user2->ratings(Page::class)->count());
        $this->assertEquals(0, $user2->raters(Page::class)->count());

        $this->assertEquals(0, $user3->ratings()->count());
        $this->assertEquals(0, $user3->raters()->count());
        $this->assertEquals(1, $user3->ratings(Page::class)->count());
        $this->assertEquals(0, $user3->raters(Page::class)->count());
    }

    public function test_average_rating_with_required_approval()
    {
        config()->set('rating.required_approval', true);

        /** @var User $user */
        $user = factory(User::class)->create();
        /** @var Page $page */
        $page = factory(Page::class)->create();

        $user->rate($page, 5);

        $this->assertEquals(0, $page->averageRating(User::class, true));
    }

    public function test_rateable_model_with_required_approval()
    {
        config()->set('rating.required_approval', true);

        /** @var User $user */
        $user = factory(User::class)->create();
        /** @var Page $page */
        $page = factory(Page::class)->create();

        $user->rate($page, 5);

        $page = $user->ratings($page)->first();
        $this->assertNull($page->rating->approved_at);
    }

    public function test_rateable_model_without_required_approval()
    {
        config()->set('rating.required_approval', false);

        /** @var User $user */
        $user = factory(User::class)->create();
        /** @var Page $page */
        $page = factory(Page::class)->create();

        $user->rate($page, 5);

        $page = $user->ratings($page)->first();
        $this->assertNotNull($page->rating->approved_at);
    }

    public function test_approve_model_rating()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        /** @var Page $page */
        $page = factory(Page::class)->create();
        $user->rate($page, 5);

        $rating = Rating::first();
        $rating->approve();

        $this->assertInstanceOf(Carbon::class, $rating->approved_at);
    }
}
