<?php namespace NAEkb\Pages\Components;

use Cms\Classes\ComponentBase;
use NAEkb\Integrations\Models\IntegrationsSettings;
use Response;

class VCard extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name' => 'naekb.pages::lang.snippet.vcard_name',
            'description' => 'naekb.pages::lang.snippet.vcard_desc',
            'icon' => 'icon-book',
            'snippetAjax' => true
        ];
    }

    public function defineProperties()
    {
        $types = [
            'phones' => __('naekb.pages::lang.snippet.vcard_phone'),
            'emails' => __('naekb.pages::lang.snippet.vcard_mail'),
        ];

        $contact = IntegrationsSettings::first();
        foreach ($contact?->others ?? [] as $other) {
            if (!empty($other['type'])) {
                $types[$other['type']] = $other['type'];
            }
        }

        return [
            'contact_types' => [
                'title' => 'naekb.pages::lang.snippet.vcard_name',
                'description' => 'naekb.pages::lang.snippet.vcard_contacts_desc',
                'type' => 'set',
                'items' => $types
            ]
        ];
    }

    public function onRender()
    {
        $contacts = [];
        $types = $this->property('contact_types') ?? [];

        $contact = IntegrationsSettings::first();

        if (in_array('phones', $types)) {
            foreach ($contact?->phones ?? [] as $phone) {
                $tiny = str_replace(' ', '', $phone['phone']);
                $tiny = str_replace('-', '', $tiny);
                $contacts[] = [
                    'icon' => 'phone',
                    'contact' => $phone['phone'],
                    'label' => $phone['label'],
                    'desc' => $phone['desc'],
                    'link' => "tel:{$tiny}"
                ];
            }
            unset($types[array_search('phones', $types)]);
        }

        if (in_array('emails', $types)) {
            foreach ($contact?->emails ?? [] as $email) {
                $contacts[] = [
                    'icon' => 'mail',
                    'contact' => $email['email'],
                    'label' => $email['label'],
                    'desc' => $email['desc'],
                    'link' => "mailto:{$email['email']}"
                ];
            }
            unset($types[array_search('emails', $types)]);
        }

        foreach ($contact?->others ?? [] as $other) {
            if (!in_array($other['type'] ?? '', $types)) {
                continue;
            }
            $contacts[] = [
                'iconImg' => 'mail',
                'contact' => $other['linkText'],
                'label' => $other['label'],
                'desc' => $other['desc'],
                'link' => $other['link'],
            ];
        }

        $this->page['contacts'] = $contacts;
    }

    public function onDownload()
    {
        $contact = IntegrationsSettings::first();
        $vcard = new \JeroenDesloovere\VCard\VCard();
        $vcard->addName($contact->last_name, $contact->first_name);
        $vcard->addCompany($contact->company);
        $vcard->addURL($contact->url);

        foreach ($contact->phones as $phone) {
            $phone['phone'] = str_replace(' ', '', $phone['phone']);
            $phone['phone'] = str_replace('-', '', $phone['phone']);
            $vcard->addPhoneNumber($phone['phone'], implode(';', $phone['types']));
        }
        foreach ($contact->emails as $email) {
            $vcard->addEmail($email['email']);
        }

        return Response::streamDownload(function() use ($vcard) {
            echo $vcard->getOutput();
        }, 'na-contact.vcf');
    }
}
