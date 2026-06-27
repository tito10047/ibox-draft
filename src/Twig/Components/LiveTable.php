<?php

namespace App\Twig\Components;

use App\Enum\ComplaintType;
use App\Service\ComplaintService;
use App\Twig\Components\IboxTable\IBoxLiveTable;
use App\Twig\Components\IboxTable\IBoxLiveTableFilter;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class LiveTable
{
    use IBoxLiveTable;
    use DefaultActionTrait;
    use IBoxLiveTableFilter;

    #[LiveProp]
    public ?string $title = 'Kuriózne sťažnosti';

    #[LiveProp(writable: true)]
    public ?string $type = 'All';

    public function __construct(
        private ComplaintService $complaintService,
        private PaginatorInterface $paginator
    ) {
        $this->perPage = 10;
        $this->orderBy = "date";
        $this->orderDirection = "DESC";
    }

    public function paginator(): PaginationInterface
    {
        $offset = ($this->page - 1) * $this->perPage;
        $items = $this->complaintService->find(
            $this->query,
            $this->type,
            $this->orderBy,
            $this->orderDirection ?? 'ASC',
            $this->perPage,
            $offset
        );

        $totalItems = $this->complaintService->count($this->query, $this->type);

        $pagination = $this->paginator->paginate(
            $items, // Tu KnpPagination pri poli berie celé pole, čo nechceme ak už máme offsetované.
            $this->page,
            $this->perPage,
            ['distinct' => false]
        );

        // Musíme manuálne prepísať total count, pretože sme mu dali len výsek
        $pagination->setTotalItemCount($totalItems);
        $pagination->setItems($items);

        return $pagination;
    }

    public function createPaginationQueryBuilder(): \Doctrine\ORM\QueryBuilder
    {
        throw new \LogicException("Nepoužíva sa QueryBuilder, používame ComplaintService.");
    }

    public function onTollReset(): void
    {
        $this->type = 'All';
    }

    public function getComplaintTypes(): array
    {
        return array_merge(['All'], array_map(fn(ComplaintType $t) => $t->value, ComplaintType::cases()));
    }
}
