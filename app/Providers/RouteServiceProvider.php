<?php

public function register(): void
{
    $this->app
        ->container()
        ->singleton(
            Router::class,
            fn () => new Router()
        );
}

public function boot(): void
{
    //
}
