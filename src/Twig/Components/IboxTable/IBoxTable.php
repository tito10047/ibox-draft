<?php

namespace App\Twig\Components\IboxTable;

use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\PostMount;

#[AsTwigComponent("IBoxTable",template: 'components/IBoxTable/IboxTable.html.twig')]
class IBoxTable
{
    public bool $collapsable = false;
    public ?string $title = null;
    public ?PaginationInterface $pagination = null;
    public int $perPage = 10;
    public array $pages = [10, 25, 50, 100];
    public bool $editable = false;
    public bool $editing = false;
    public bool $canEdit = true;
    public ?bool $cantEditMessage = null;
    public ?\Symfony\Component\Form\FormView $form = null;
    public string $footerClass = 'ibox-footer';
    public string $wrapperClass = 'ibox-content preloader-wrapper';
    public bool $hideFooter = false;
    public ?bool $tools = false;
    public bool $hidden = false;
    public ?bool $hideCancel = false;


    #[PostMount]
    public function postMount() {
        $this->wrapperClass = $this->wrapperClass . ' ' . ($this->editable ? 'p-3' : 'p-1');
    }
}
