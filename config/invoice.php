<?php

return [
    // These values control the static seller and branding details shown on invoice PDFs.
    'company' => [
        'name' => 'BIOGENIX INC. PVT. LTD.',
        'gstin' => '09AAGCB6941G1ZJ',
        'state_code' => '9',
        'address_lines' => [
            'B19/A, S.I.L. Ancillary Estate,',
            'Amausi Industrial Area, Nadarganj-226008,',
            'Lucknow (U.P.) Mob.: 9889485222, 9616105666',
            'E-mail: biogenix2007@yahoo.com, info@biogenixinc.com',
        ],
        'logo_path' => 'upload/icons/logo.jpg',
    ],

    // These values control the document title and static labels.
    'branding' => [
        'document_title' => 'PROFORMA INVOICE',
        'amount_in_words_label' => 'Amount in Rupees:',
        'auto_generated_text' => 'Auto-Generated Proforma,',
        'signature_note' => 'Signature not required.',
        'bank_heading' => 'Bank Details:',
    ],

    // These values control PDF rendering defaults.
    'pdf' => [
        'paper' => 'a4',
        'orientation' => 'portrait',
        'margin_top' => '10mm',
        'margin_right' => '10mm',
        'margin_bottom' => '12mm',
        'margin_left' => '10mm',
        'currency' => 'INR',
        'date_display_format' => 'dS M-Y',
        'dated_display_format' => 'd-m-Y',
        'minimum_item_rows' => 16,
    ],

    // These values control the static terms section.
    'terms' => [
        'heading' => 'TERMS : E.&O.E.',
        'items' => [
            'Supply within 4-5 week after confirmation order along with 100% advance payment.',
            'All Disputes are subject to Lucknow Jurisdiction only',
        ],
    ],

    // These values control the static bank details block.
    'bank' => [
        'lines' => [
            '1. Bank of Baroda Lucknow, Account No: 00500500000114, IFSC Code: BARB0HAZARA.',
        ],
    ],
];
