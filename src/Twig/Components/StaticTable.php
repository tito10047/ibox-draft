<?php

namespace App\Twig\Components;

use App\Service\ComplaintService;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class StaticTable
{
    public string $title = 'Latest Complaints';

    public function __construct(
        private ComplaintService $complaintService
    ) {
    }

    public function getItems(): array
    {
        return $this->complaintService->find(
            orderBy: 'date',
            order: 'DESC',
            perPage: 5
        );
    }
}
