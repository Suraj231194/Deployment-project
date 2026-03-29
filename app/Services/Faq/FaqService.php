<?php

namespace App\Services\Faq;

use App\Models\Faq\Faq;
use Illuminate\Support\Collection;

class FaqService
{
    // fetch active FAQs and return them to the controller.
    public function getFaqs(): Collection
    {
        return Faq::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }
}
