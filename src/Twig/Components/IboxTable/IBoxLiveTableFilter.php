<?php
/*
 * This file is part of the Progressive Image Bundle.
 *
 * (c) Jozef Môstka <https://github.com/tito10047/progressive-image-bundle>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Twig\Components\IboxTable;

use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\TwigComponent\Attribute\PreMount;

trait IBoxLiveTableFilter {
    #[LiveProp]
    public ?bool $tools = false;

    #[LiveAction]
    public function toggleTools(): void {
        if (method_exists($this, "toggle")) {
            $this->tools = $this->toggle("tools", false, $this->tools);
        }else {
            $this->tools = !$this->tools;
        }
        $this->onTollReset();
    }

    #[PreMount]
    public function postMountLiveFilter(array $data): array {
        if (($data["query"]??null)) {
            $this->tools = true;
        }
        return $data;
    }
    #[LiveAction]
    public function search(): void
    {
        $this->rowId = uniqid();
    }

    #[LiveAction]
    public function resetSearch(): void
    {
        $this->query = null;
        $this->page = 1;
        $this->rowId = uniqid();
    }

    abstract public function onTollReset():void ;
}
