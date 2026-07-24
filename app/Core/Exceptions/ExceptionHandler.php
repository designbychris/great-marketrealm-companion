<?php

namespace GreatMarketrealmCompanion\Core\Exceptions;

use GreatMarketrealmCompanion\Core\Http\ResponseFactory;
use GreatMarketrealmCompanion\Core\Session\FlashStore;
use GreatMarketrealmCompanion\Core\Http\Validation\ValidationException;
use Throwable;

defined('ABSPATH') || exit;

/**
 * Exception Handler.
 *
 * Responsible for converting exceptions into framework responses.
 *
 * @package MarketrealmCompanion
 * @since 0.7.0
 */
class ExceptionHandler
{
    /**
     * Create the exception handler.
     */
    public function __construct(
        protected FlashStore $flash,
        protected ResponseFactory $response
    ) {
    }

    /**
     * Handle an exception.
     */
    public function handle(
        Throwable $exception
    ): mixed {

        /*
         * Validation exceptions.
         */
        if ($exception instanceof ValidationException) {

            $this->flash->flashErrors(
                $exception->errors()
            );

            $this->flash->flashOldInput(
                $exception->oldInput()
            );

            $this->flash->error(
                $exception->getMessage()
            );

            return $this->response->redirectBack();
        }

        /*
         * Everything else.
         */
        return $this->renderThrowable(
            $exception
        );
    }

    /**
     * Render an unhandled exception.
     */
    protected function renderThrowable(
        Throwable $exception
    ): mixed {

        // Logging will eventually go here.

        if (defined('WP_DEBUG') && WP_DEBUG) {

            throw $exception;

        }

        status_header(500);

        wp_die(
            esc_html__(
                'An unexpected error occurred.',
                'greatmarketrealmcompanion'
            ),
            esc_html__(
                'Application Error',
                'greatmarketrealmcompanion'
            ),
            [
                'response' => 500,
            ]
        );
    }
}
