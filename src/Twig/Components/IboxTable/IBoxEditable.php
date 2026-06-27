<?php
/**
 * Created by PhpStorm.
 * User: Jozef Môstka
 * Date: 27. 9. 2024
 * Time: 11:56
 */

namespace App\Twig\Components\IboxTable;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;

trait IBoxEditable {

    use ComponentWithFormTrait {
        ComponentWithFormTrait::resetForm as private resetFormTrait;
    }

    #[LiveProp]
    public bool    $editing     = false;
    #[LiveProp]
    public ?string $editRight = null;
    public ?string $editDeniedMessage = null;

    #[LiveAction]
    public abstract function save(EntityManagerInterface $emRds): ?Response;

    #[LiveAction]
    public function toggleEdit(): void {
        if (!$this->hasAccessToEdit()) {
            $this->addFlash('warning', $this->editDeniedMessage ?? "Nemáte prístup k úpravám!");
            $this->editing = false;
            return;
        }
        $this->editing = !$this->editing;
    }

    private function resetForm():void {
        $this->editing = false;
        $this->resetFormTrait();
    }

    protected abstract function instantiateEditForm(bool $disabled):FormInterface;

    /**
     * @internal
     */
    protected function instantiateForm(): FormInterface{
        $disabled = !$this->editing || !$this->hasAccessToEdit();
        return  $this->instantiateEditForm($disabled);
    }

    private function getDataModelValue(): ?string
    {
        return 'norender|*';
    }

    public function hasAccessToEdit(): bool
    {
        if (!$this->editRight){
            return true;
        }
        if (!property_exists($this,"security")){
            throw new \LogicException("Inject Security service to use role checker!");
        }
        if ($this->security->isGranted($this->editRight)){
            return true;
        }
        $this->editDeniedMessage = "Pre prístup k úpravám je potrebné mať právo {$this->editRight}!";
        return false;
    }

}
