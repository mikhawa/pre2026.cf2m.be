<?php

declare(strict_types=1);

namespace App\Tests\Service\Layout;

use App\Entity\Formation;
use App\Entity\Page;
use App\Entity\User;
use App\Repository\FormationHistoryRepository;
use App\Repository\FormationRepository;
use App\Repository\PageHistoryRepository;
use App\Repository\PageRepository;
use App\Repository\WorksHistoryRepository;
use App\Service\Layout\LayoutPropsBuilder;
use App\Twig\NavigationExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Asset\Packages;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Les props des îlots React ne doivent contenir que ce que l'utilisateur a le
 * droit de voir : c'est ici (et non dans React) que les rôles sont vérifiés.
 */
class LayoutPropsBuilderTest extends TestCase
{
    /**
     * @param list<string> $roles rôles accordés (IS_AUTHENTICATED_FULLY inclus si connecté)
     */
    private function buildBuilder(array $roles, ?User $user = null, int $pendingRevisions = 0): LayoutPropsBuilder
    {
        $formation = (new Formation())->setTitle('Développeur web')->setSlug('dev-web')->setStatus('recruiting');
        $page = (new Page())->setTitle('Portes ouvertes')->setSlug('portes-ouvertes');

        $formationRepo = $this->createStub(FormationRepository::class);
        $formationRepo->method('findAllPublished')->willReturn([$formation]);
        $pageRepo = $this->createStub(PageRepository::class);
        $pageRepo->method('findAllPublished')->willReturn([$page]);
        $formationHistory = $this->createStub(FormationHistoryRepository::class);
        $formationHistory->method('countPending')->willReturn($pendingRevisions);
        $pageHistory = $this->createStub(PageHistoryRepository::class);
        $pageHistory->method('countPending')->willReturn(0);
        $worksHistory = $this->createStub(WorksHistoryRepository::class);
        $worksHistory->method('countPending')->willReturn(0);

        $navigation = new NavigationExtension($formationRepo, $pageRepo, $formationHistory, $pageHistory, $worksHistory);

        $security = $this->createStub(Security::class);
        $security->method('isGranted')->willReturnCallback(fn (string $attribute) => \in_array($attribute, $roles, true));
        $security->method('getUser')->willReturn($user);

        $urlGenerator = $this->createStub(UrlGeneratorInterface::class);
        $urlGenerator->method('generate')->willReturnCallback(
            fn (string $route, array $parameters = []) => '/'.$route.($parameters ? '/'.implode('/', $parameters) : '')
        );

        $packages = $this->createStub(Packages::class);
        $packages->method('getUrl')->willReturnCallback(fn (string $path) => '/'.$path);

        return new LayoutPropsBuilder($navigation, $security, $urlGenerator, $packages);
    }

    private function user(): User
    {
        return (new User())->setEmail('marie@cf2m.be')->setUserName('Marie');
    }

    public function testAnonymousVisitorHasNoUserMenu(): void
    {
        $header = $this->buildBuilder([])->header();

        self::assertNull($header['user']);
        self::assertSame('/app_login', $header['loginUrl']);
    }

    public function testHeaderListsFormationsAndActivities(): void
    {
        $header = $this->buildBuilder([])->header();
        $byLabel = array_column($header['nav'], null, 'label');

        self::assertSame(
            [['label' => 'Développeur web', 'url' => '/app_formation_show/dev-web', 'recruiting' => true]],
            $byLabel['Nos formations']['items']
        );
        self::assertSame(
            [['label' => 'Portes ouvertes', 'url' => '/app_page_show/portes-ouvertes']],
            $byLabel['Nos activités']['items']
        );
    }

    public function testSimpleUserHasNoAdministrationLink(): void
    {
        $header = $this->buildBuilder(['IS_AUTHENTICATED_FULLY'], $this->user())->header();

        self::assertSame('Marie', $header['user']['name']);
        self::assertNull($header['user']['adminUrl']);
        self::assertNull($header['user']['pendingRevisions']);
    }

    public function testStagiaireSeesAdministrationLinkWithoutPendingBadge(): void
    {
        $header = $this->buildBuilder(['IS_AUTHENTICATED_FULLY', 'ROLE_STAGIAIRE'], $this->user(), 4)->header();

        self::assertSame('/admin', $header['user']['adminUrl']);
        self::assertNull($header['user']['pendingRevisions']);
    }

    public function testAdminSeesPendingRevisionsBadge(): void
    {
        $header = $this->buildBuilder(['IS_AUTHENTICATED_FULLY', 'ROLE_STAGIAIRE', 'ROLE_ADMIN'], $this->user(), 4)->header();

        self::assertSame('/admin', $header['user']['adminUrl']);
        self::assertSame(4, $header['user']['pendingRevisions']);
    }

    public function testAdminWithoutPendingRevisionsHasNoBadge(): void
    {
        $header = $this->buildBuilder(['IS_AUTHENTICATED_FULLY', 'ROLE_STAGIAIRE', 'ROLE_ADMIN'], $this->user(), 0)->header();

        self::assertNull($header['user']['pendingRevisions']);
    }

    public function testRememberedButNotFullyAuthenticatedUserIsTreatedAsAnonymous(): void
    {
        $header = $this->buildBuilder(['IS_AUTHENTICATED_REMEMBERED'], $this->user())->header();

        self::assertNull($header['user']);
    }

    public function testHeroAndFooterPropsAreSerializable(): void
    {
        $builder = $this->buildBuilder([]);

        self::assertJson(json_encode($builder->hero(), \JSON_THROW_ON_ERROR));
        self::assertJson(json_encode($builder->footer(), \JSON_THROW_ON_ERROR));
        self::assertCount(2, $builder->hero()['stats']);
    }
}
