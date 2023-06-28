<?php
/**
 * Artist service.
 */

namespace App\Service;

use App\Repository\ArtistRepository;
use App\Repository\TaskRepository;
use App\Entity\Artist;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class ArtistService.
 */
class ArtistService implements ArtistServiceInterface
{
    /**
     * Artist repository.
     */
    private ArtistRepository $artistRepository;

    /**
     * Task repository.
     */
    private TaskRepository $taskRepository;

    /**
     * Paginator.
     */
    private PaginatorInterface $paginator;

    /**
     * Entity Manager.
     */
    private EntityManagerInterface $entityManager;

    /**
     * Constructor.
     *
     * @param ArtistRepository       $artistRepository Artist repository
     * @param TaskRepository         $taskRepository   Task repository
     * @param PaginatorInterface     $paginator        Paginator
     * @param EntityManagerInterface $entityManager    Entity Manager
     */
    public function __construct(
        ArtistRepository $artistRepository,
        TaskRepository $taskRepository,
        PaginatorInterface $paginator,
        EntityManagerInterface $entityManager
    ) {
        $this->artistRepository = $artistRepository;
        $this->taskRepository = $taskRepository;
        $this->paginator = $paginator;
        $this->entityManager = $entityManager;
    }

    /**
     * Get paginated list.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface Paginated list
     */
    public function getPaginatedList(int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->artistRepository->queryAll(),
            $page,
            ArtistRepository::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Save entity.
     *
     * @param Artist $artist Artist entity
     */
    public function save(Artist $artist): void
    {
        $this->artistRepository->save($artist);
    }

    /**
     * Delete entity.
     *
     * @param Artist $artist Artist entity
     */
    public function delete(Artist $artist): void
    {
        $this->entityManager->remove($artist);
        $this->entityManager->flush();
    }

    /**
     * Can Artist be deleted?
     *
     * @param Artist $artist Artist entity
     *
     * @return bool Result
     */
    public function canBeDeleted(Artist $artist): bool
    {
        try {
            $result = $this->taskRepository->countByArtist($artist);

            return !($result > 0);
        } catch (NoResultException|NonUniqueResultException) {
            return false;
        }
    }
}
