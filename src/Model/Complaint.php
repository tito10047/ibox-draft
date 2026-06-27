<?php

namespace App\Model;

use App\Enum\ComplaintType;

class Complaint
{
    public function __construct(
        public ?int $id = null,
        public ?string $title = null,
        public ?\DateTimeInterface $date = null,
        public ?string $imageUrl = null,
        public ?string $description = null,
        public ?ComplaintType $type = null
    ) {
    }
}
