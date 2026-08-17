<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Parties\Http;

use PHPUnit\Framework\TestCase;

final class PartyRoutesControllerRegressionTest extends TestCase
{
    public function testPartyRoutesExposeFullFellowshipHttpContract(): void
    {
        $root = dirname(__DIR__, 5);
        $routes = file_get_contents(
            $root . '/app/Modules/Parties/Routes.php'
        );

        self::assertIsString($routes);

        foreach ([
            "'/parties'",
            "'/parties/create'",
            "'/parties/{id}'",
            "'/parties/{id}/edit'",
            "'/parties/{id}/members'",
            "'/parties/{id}/members/{character}'",
            "'/parties/{id}/members/{character}/role'",
        ] as $path) {
            self::assertStringContainsString(
                $path,
                $routes
            );
        }

        foreach ([
            "'index'",
            "'create'",
            "'store'",
            "'show'",
            "'edit'",
            "'update'",
            "'destroy'",
            "'addMember'",
            "'removeMember'",
            "'updateMemberRole'",
        ] as $method) {
            self::assertStringContainsString(
                $method,
                $routes
            );
        }
    }

    public function testPartiesKingdomContributesRouteFileAndFellowshipNavigation(): void
    {
        $root = dirname(__DIR__, 5);
        $kingdom = file_get_contents(
            $root . '/app/Kingdoms/PartiesKingdom.php'
        );

        self::assertIsString($kingdom);
        self::assertStringContainsString(
            "app/Modules/Parties/Routes.php",
            $kingdom
        );
        self::assertStringContainsString(
            'registerNavigation',
            $kingdom
        );
        self::assertStringContainsString(
            'Icons::PARTY',
            $kingdom
        );
    }

    public function testControllerDelegatesMutationsToApplicationActions(): void
    {
        $root = dirname(__DIR__, 5);
        $controller = file_get_contents(
            $root
            . '/app/Modules/Parties/Controllers/'
            . 'PartyController.php'
        );

        self::assertIsString($controller);

        foreach ([
            '$this->createParty->handle(',
            '$this->addMember->handle(',
            '$this->removeMember->handle(',
            '$this->changeRole->handle(',
            '$this->renameParty->handle(',
            '$this->deleteParty->handle(',
        ] as $delegation) {
            self::assertStringContainsString(
                $delegation,
                $controller
            );
        }

        self::assertStringNotContainsString(
            'wp_insert_post(',
            $controller
        );
        self::assertStringNotContainsString(
            'update_post_meta(',
            $controller
        );
    }

    public function testControllerResolvesOwnerFromSignedInGuildAccount(): void
    {
        $root = dirname(__DIR__, 5);
        $controller = file_get_contents(
            $root
            . '/app/Modules/Parties/Controllers/'
            . 'PartyController.php'
        );

        self::assertIsString($controller);
        self::assertStringContainsString(
            'is_user_logged_in()',
            $controller
        );
        self::assertStringContainsString(
            'get_current_user_id()',
            $controller
        );
        self::assertStringContainsString(
            'PartyOwnerId::fromInt(',
            $controller
        );
    }

    public function testPartyWriteRequestsRequireAuthentication(): void
    {
        $root = dirname(__DIR__, 5);

        foreach ([
            'StorePartyRequest.php',
            'UpdatePartyRequest.php',
            'AddPartyMemberRequest.php',
            'UpdatePartyMemberRoleRequest.php',
        ] as $file) {
            $request = file_get_contents(
                $root
                . '/app/Modules/Parties/Requests/'
                . $file
            );

            self::assertIsString($request);
            self::assertStringContainsString(
                'return is_user_logged_in();',
                $request
            );
        }
    }

    public function testMembershipRequestsValidateRoleAndCharacterIdentity(): void
    {
        $root = dirname(__DIR__, 5);
        $add = file_get_contents(
            $root
            . '/app/Modules/Parties/Requests/'
            . 'AddPartyMemberRequest.php'
        );
        $role = file_get_contents(
            $root
            . '/app/Modules/Parties/Requests/'
            . 'UpdatePartyMemberRoleRequest.php'
        );

        self::assertIsString($add);
        self::assertIsString($role);
        self::assertStringContainsString(
            "'character_id'",
            $add
        );
        self::assertStringContainsString(
            "'min:26'",
            $add
        );
        self::assertStringContainsString(
            "'max:26'",
            $add
        );
        self::assertStringContainsString(
            "'in:leader,member'",
            $add
        );
        self::assertStringContainsString(
            "'in:leader,member'",
            $role
        );
    }

    public function testAdminPostNonceContractCoversAllPartyMutations(): void
    {
        $root = dirname(__DIR__, 5);
        $provider = file_get_contents(
            $root
            . '/app/Providers/FrontendServiceProvider.php'
        );

        self::assertIsString($provider);
        self::assertStringContainsString(
            "return 'gmrc_create_party';",
            $provider
        );
        self::assertStringContainsString(
            "'gmrc_party_'",
            $provider
        );
        self::assertStringContainsString(
            "'gmrc_party_members_'",
            $provider
        );
        self::assertStringContainsString(
            "#^parties/([^/]+)/members",
            $provider
        );
    }

    public function testScaffoldFormsUseTheSameNonceContracts(): void
    {
        $root = dirname(__DIR__, 5);

        $create = file_get_contents(
            $root . '/app/Modules/Parties/Views/create.php'
        );
        $edit = file_get_contents(
            $root . '/app/Modules/Parties/Views/edit.php'
        );
        $show = file_get_contents(
            $root . '/app/Modules/Parties/Views/show.php'
        );
        $member = file_get_contents(
            $root
            . '/app/Views/components/entries/'
            . 'fellowship-member.php'
        );

        self::assertIsString($create);
        self::assertIsString($edit);
        self::assertIsString($show);
        self::assertIsString($member);

        self::assertStringContainsString(
            "'gmrc_create_party'",
            $create
        );
        self::assertStringContainsString(
            "'gmrc_party_' . \$id",
            $edit
        );
        self::assertStringContainsString(
            "'gmrc_party_members_' . \$id",
            $show
        );
        self::assertStringContainsString(
            "'gmrc_party_members_' . \$partyId",
            $member
        );
        self::assertStringContainsString(
            'name="_method" value="PUT"',
            $member
        );
        self::assertStringContainsString(
            'name="_method" value="DELETE"',
            $member
        );
    }

    public function testControllerIsRegisteredAndViewsExist(): void
    {
        $root = dirname(__DIR__, 5);
        $provider = file_get_contents(
            $root . '/app/Modules/Parties/PartiesServiceProvider.php'
        );

        self::assertIsString($provider);
        self::assertStringContainsString(
            'PartyController::class',
            $provider
        );

        foreach ([
            'index.php',
            'create.php',
            'show.php',
            'edit.php',
        ] as $view) {
            self::assertFileExists(
                $root
                . '/app/Modules/Parties/Views/'
                . $view
            );
        }
    }
}
