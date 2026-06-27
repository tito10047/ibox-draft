<?php

namespace App\Controller;

use App\Service\ComplaintService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class IndexController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function index(): Response
    {
        return $this->render('index/index.html.twig', [
            'title' => 'City Council Complaints',
        ]);
    }

    #[Route('/complaint/{id}/edit', name: 'app_complaint_edit')]
    public function edit(int $id, ComplaintService $complaintService): Response
    {
        $complaint = $complaintService->get($id);
        if (!$complaint) {
            throw $this->createNotFoundException('Complaint not found');
        }

        return $this->render('index/edit.html.twig', [
            'complaintId' => $id,
            "complaint" => $complaint,
        ]);
    }
}
