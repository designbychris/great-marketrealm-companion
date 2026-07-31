<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Http;

use GreatMarketrealmCompanion\Core\Http\Request;
use PHPUnit\Framework\TestCase;

final class RequestTest extends TestCase
{
    /**
     * Preserve the original PHP request globals.
     *
     * @var array<string, mixed>
     */
    private array $originalGet = [];

    /**
     * @var array<string, mixed>
     */
    private array $originalPost = [];

    /**
     * @var array<string, mixed>
     */
    private array $originalServer = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->originalGet = $_GET;
        $this->originalPost = $_POST;
        $this->originalServer = $_SERVER;

        $_GET = [];
        $_POST = [];
        $_SERVER = [];
    }

    protected function tearDown(): void
    {
        $_GET = $this->originalGet;
        $_POST = $this->originalPost;
        $_SERVER = $this->originalServer;

        parent::tearDown();
    }

    public function testRequestCanBeCaptured(): void
    {
        $request = Request::capture();

        $this->assertInstanceOf(
            Request::class,
            $request
        );
    }

    public function testRequestDefaultsToGetMethod(): void
    {
        $request = new Request();

        $this->assertSame(
            'GET',
            $request->method()
        );
    }

    public function testRequestReturnsServerMethod(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'post';

        $request = new Request();

        $this->assertSame(
            'POST',
            $request->method()
        );
    }

    public function testRequestSupportsPutMethodSpoofing(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['_method'] = 'put';

        $request = new Request();

        $this->assertSame(
            'PUT',
            $request->method()
        );
    }

    public function testRequestSupportsPatchMethodSpoofing(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['_method'] = 'patch';

        $request = new Request();

        $this->assertSame(
            'PATCH',
            $request->method()
        );
    }

    public function testRequestSupportsDeleteMethodSpoofing(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['_method'] = 'delete';

        $request = new Request();

        $this->assertSame(
            'DELETE',
            $request->method()
        );
    }

    public function testRequestRejectsUnsupportedMethodSpoofing(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['_method'] = 'OPTIONS';

        $request = new Request();

        $this->assertSame(
            'POST',
            $request->method()
        );
    }

    public function testMethodSpoofingOnlyAppliesToPostRequests(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET['_method'] = 'DELETE';

        $request = new Request();

        $this->assertSame(
            'GET',
            $request->method()
        );
    }

    public function testRequestDefaultsToRootPath(): void
    {
        $request = new Request();

        $this->assertSame(
            '/',
            $request->path()
        );
    }

    public function testRequestReturnsCurrentPath(): void
    {
        $_SERVER['REQUEST_URI'] = '/characters/42';

        $request = new Request();

        $this->assertSame(
            '/characters/42',
            $request->path()
        );
    }

    public function testRequestRemovesQueryStringFromPath(): void
    {
        $_SERVER['REQUEST_URI'] = '/characters?page=2';

        $request = new Request();

        $this->assertSame(
            '/characters',
            $request->path()
        );
    }

    public function testRequestNormalisesTrailingSlash(): void
    {
        $_SERVER['REQUEST_URI'] = '/characters/';

        $request = new Request();

        $this->assertSame(
            '/characters',
            $request->path()
        );
    }

    public function testRequestNormalisesMissingLeadingSlash(): void
    {
        $_SERVER['REQUEST_URI'] = 'characters/42';

        $request = new Request();

        $this->assertSame(
            '/characters/42',
            $request->path()
        );
    }

    public function testRequestRetrievesPostInput(): void
    {
        $_POST['name'] = 'Auby';

        $request = new Request();

        $this->assertSame(
            'Auby',
            $request->input('name')
        );
    }

    public function testRequestRetrievesGetInput(): void
    {
        $_GET['page'] = '2';

        $request = new Request();

        $this->assertSame(
            '2',
            $request->input('page')
        );
    }

    public function testPostInputTakesPriorityOverGetInput(): void
    {
        $_GET['name'] = 'Get Auby';
        $_POST['name'] = 'Post Auby';

        $request = new Request();

        $this->assertSame(
            'Post Auby',
            $request->input('name')
        );
    }

    public function testRequestReturnsDefaultForMissingInput(): void
    {
        $request = new Request();

        $this->assertSame(
            'fallback',
            $request->input(
                'missing',
                'fallback'
            )
        );
    }

    public function testRequestUnslashesInput(): void
    {
        $_POST['name'] = 'Auby\\\'s Market';

        $request = new Request();

        $this->assertSame(
            "Auby's Market",
            $request->input('name')
        );
    }

    public function testRequestDetectsExistingInput(): void
    {
        $_GET['page'] = '2';

        $request = new Request();

        $this->assertTrue(
            $request->has('page')
        );
    }

    public function testRequestDoesNotDetectMissingInput(): void
    {
        $request = new Request();

        $this->assertFalse(
            $request->has('missing')
        );
    }

    public function testRequestHasReturnsTrueForNullValue(): void
    {
        $_POST['name'] = null;

        $request = new Request();

        $this->assertTrue(
            $request->has('name')
        );
    }

    public function testRequestReturnsAllInput(): void
    {
        $_GET = [
            'page' => '2',
            'filter' => 'fruit',
        ];

        $_POST = [
            'name' => 'Auby',
        ];

        $request = new Request();

        $this->assertSame(
            [
                'page' => '2',
                'filter' => 'fruit',
                'name' => 'Auby',
            ],
            $request->all()
        );
    }

    public function testPostInputOverridesGetInputWhenRetrievingAll(): void
    {
        $_GET['name'] = 'Get Auby';
        $_POST['name'] = 'Post Auby';

        $request = new Request();

        $this->assertSame(
            [
                'name' => 'Post Auby',
            ],
            $request->all()
        );
    }

    public function testRequestReturnsSanitisedString(): void
    {
        $_POST['name'] = '  <strong>Auby</strong>  ';

        $request = new Request();

        $this->assertSame(
            'Auby',
            $request->string('name')
        );
    }

    public function testRequestReturnsDefaultStringForMissingInput(): void
    {
        $request = new Request();

        $this->assertSame(
            'Marketgoer',
            $request->string(
                'name',
                'Marketgoer'
            )
        );
    }

    public function testRequestReturnsDefaultStringForNonScalarInput(): void
    {
        $_POST['name'] = [
            'Auby',
        ];

        $request = new Request();

        $this->assertSame(
            'Marketgoer',
            $request->string(
                'name',
                'Marketgoer'
            )
        );
    }

    public function testRequestReturnsIntegerInput(): void
    {
        $_POST['level'] = '12';

        $request = new Request();

        $this->assertSame(
            12,
            $request->integer('level')
        );
    }

    public function testRequestReturnsAbsoluteInteger(): void
    {
        $_POST['level'] = '-12';

        $request = new Request();

        $this->assertSame(
            12,
            $request->integer('level')
        );
    }

    public function testRequestReturnsDefaultIntegerForMissingInput(): void
    {
        $request = new Request();

        $this->assertSame(
            5,
            $request->integer(
                'level',
                5
            )
        );
    }

    public function testRequestReturnsDefaultIntegerForNonScalarInput(): void
    {
        $_POST['level'] = [
            12,
        ];

        $request = new Request();

        $this->assertSame(
            5,
            $request->integer(
                'level',
                5
            )
        );
    }

    public function testRequestMatchesHttpMethod(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $request = new Request();

        $this->assertTrue(
            $request->isMethod('post')
        );
    }

    public function testRequestDoesNotMatchDifferentHttpMethod(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $request = new Request();

        $this->assertFalse(
            $request->isMethod('get')
        );
    }
}
