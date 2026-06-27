<?php

namespace App\Twig\Components;

use App\Form\ComplaintFormType;
use App\Model\Complaint;
use App\Service\ComplaintService;
use App\Twig\Components\IboxTable\IBoxEditable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\Attribute\PostHydrate;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class ComplaintEdit extends AbstractController
{
    use DefaultActionTrait;
    use IBoxEditable;

    #[LiveProp]
    public ?Complaint $initialComplaint = null;

    public ?Complaint $complaint = null;
    #[LiveProp]
    public ?int      $complaintId;

    public function __construct(
        private ComplaintService $complaintService
    ) {
    }

    public function mount(?int $complaintId = null): void
    {
        $this->complaintId = $complaintId;
        $this->complaint = $this->complaintService->get($complaintId);
    }

    #[PostHydrate]
    public function postHydrate(): void
    {
        if ($this->complaintId) {
            $this->complaint = $this->complaintService->get($this->complaintId);
        }
    }

    protected function instantiateEditForm(bool $disabled): FormInterface
    {
        return $this->createForm(ComplaintFormType::class, $this->complaint, [
            'disabled' => $disabled,
        ]);
    }

    #[LiveAction]
    public function save(EntityManagerInterface $emRds): ?Response
    {
        $this->submitForm();

        /** @var Complaint $complaint */
        $complaint = $this->getForm()->getData();
        $this->complaintService->update($complaint);

        $this->addFlash('success', 'Complaint updated successfully (in memory).');

        $this->editing = false;

        return null;
    }
}
