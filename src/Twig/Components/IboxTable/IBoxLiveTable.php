<?php
/**
 * Created by PhpStorm.
 * User: Jozef Môstka
 * Date: 27. 9. 2024
 * Time: 14:26
 */

namespace App\Twig\Components\IboxTable;

use Doctrine\ORM\QueryBuilder;
use Knp\Component\Pager\Pagination\PaginationInterface;
use LogicException;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;

trait IBoxLiveTable
{
    #[LiveProp(writable: true)]
    public ?string $query = null;
    #[LiveProp(writable: true, onUpdated: "onPerPageUpdated")]
    public int $perPage = 10;
    #[LiveProp(writable: true)]
    public ?string $orderBy = null;

    #[LiveProp()]
    public bool    $hidden         = false;
    #[LiveProp(writable: true)]
    public ?string $orderDirection = null;
    #[LiveProp]
    public ?string $rowId = null;
	#[LiveProp]
	public string $wrapperClass = 'ibox-content p-1 preloader-wrapper';
	#[LiveProp]
	public string $footerClass = 'ibox-footer';
    private ?PaginationInterface $pager = null;
    private int $page = 1;

    #[LiveProp]
    public bool $paginatorPlus = false;

    public function onPerPageUpdated($previousValue): void {

    }

    public function onHiddenUpdated(bool $previousValue):void {
    }

    public function paginator(): PaginationInterface
    {
        if (!property_exists($this, 'paginator')) {
            throw new LogicException("PaginatorInterface \$paginator is not injected");
        }
        if ($this->pager !== null) {
            return $this->pager;
        }

        if ($this->paginatorPlus) {
            return $this->pager = $this->makePaginatorPlus();
        }

        $qb = $this->createPaginationQueryBuilder();

        if ($this->orderBy) {
            $qb->addOrderBy($this->orderBy, $this->orderDirection);
        }

        return $this->pager = $this->paginator->paginate(
            $qb,
            $this->page,
            $this->perPage
            , [
                "size" => 'small',
            ]
        );
    }

    public function resetPager():void {
        $this->pager = null;
    }

    public function makePaginatorPlus(): PaginationInterface {
        $qb      = $this->createPaginationQueryBuilder();
        $countQb = clone $qb;

        $max = $this->perPage * 100;

        if ($this->page == -99 || $this->page* $this->perPage >$max) {
            $totalCount = $countQb->select("COUNT(f.id)")->getQuery()->getSingleScalarResult();
            if ($this->page == -99) {
                $this->page = ceil($totalCount / $this->perPage);
            }
        }else {
            $countQb->select("1")->setMaxResults($max);
            $totalCount = count($countQb->getQuery()->getArrayResult());
        }
        $qb->setFirstResult(($this->page - 1) * $this->perPage)
            ->setMaxResults($this->perPage);

        if ($this->orderBy) {
            $qb->addOrderBy($this->orderBy, $this->orderDirection);
        }


        $pagination = $this->paginator->paginate([], $this->page, $this->perPage);

        $pagination->setTotalItemCount($totalCount);
        $pagination->setItems($qb->getQuery()->getResult());

        return $pagination;
    }

    public function getRows(): iterable
    {
        return $this->paginator()->getItems();
    }

    #[LiveAction]
    public function paginate(#[LiveArg] int $page): void
    {
        $this->page = $page;
        $this->rowId = uniqid();
    }


    #[LiveAction]
    public function toggleComponent(): void
    {
        $this->hidden = !$this->hidden;
        $this->onHiddenUpdated(!$this->hidden);
    }

    #[LiveAction]
    public function setOrder(#[LiveArg] string $orderBy, #[LiveArg] string $orderDirection): void
    {
        $this->orderBy = $orderBy;
        $this->orderDirection = $orderDirection ?: null;
        $this->page = 1;
        $this->rowId = uniqid();
    }

    public function order(string $column, string $label): string
    {
        $direction = 'ASC';
        $arrow = '';
        if ($this->orderBy === $column) {
            $direction = match ($this->orderDirection) {
                'ASC' => 'DESC',
                'DESC' => null,
                null => 'ASC',
            };
            $arrow = match ($this->orderDirection) {
                null => '',
                'ASC' => '<span class="column-order">▲</span>',
                'DESC' => '<span class="column-order">▼</span>',
            };
        }

        return <<<HTML
        <a href="javascript:void(0);"
            data-action="live#action"
            data-live-order-by-param="{$column}"
            data-live-order-direction-param="{$direction}"
            data-live-action-param="setOrder"
            >{$label} {$arrow}</a>
        HTML;
    }

    abstract function createPaginationQueryBuilder(): QueryBuilder;

}
