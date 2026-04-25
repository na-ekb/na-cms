<?php namespace NAEkb\Pages\Components;

use Cms\Classes\ComponentBase;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use NaEkb\Groups\Models\Group;
use NaEkb\Groups\Enums\Weekdays;
use NaEkb\Groups\Models\GroupMeeting;
use October\Rain\Support\Facades\Site;
use October\Rain\Exception\ValidationException;

class Meetings extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name' => 'naekb.pages::lang.snippet.meetings_name',
            'description' => 'naekb.pages::lang.snippet.meetings_desc',
            'icon' => 'icon-book',
            'snippetAjax' => true
        ];
    }

    public function onRender()
    {
        $this->addJs('/plugins/naekb/pages/assets/js/meetings.js', 'NAEkb.Pages');
        $this->onFilter();
        $this->page['cities'] = Group::pluck('city')->unique()->filter()->toArray();
        $this->page['locations'] = Group::pluck('location')->unique()->filter()->toArray();
        $this->page['weekdays'] = Weekdays::asSelectArray();
    }

    public function onFilter()
    {
        $data = $this->processRequest(request());
        if (!is_array($data)) {
            return $data;
        }

        $query = GroupMeeting::with('group')->day($data['date']);
        $queryGroups = Group::with('meetings')->whereHas('meetings', function ($query) use ($data) {
            return $query->day($data['date']);
        });
        $queryGroupsWeek = Group::with('meetings');

        if (!empty($data['city'])) {
            $query->city($data['city']);
            $queryGroups->where('city', $data['city']);
            $queryGroupsWeek->where('city', $data['city']);
        }
        if (!empty($data['location'])) {
            $query->location($data['location']);
            $queryGroups->where('location', $data['location']);
            $queryGroupsWeek->where('location', $data['location']);
        }
        $this->page['meetings'] = $query->orderBy('time')->get();
        $this->page['groups'] = $queryGroups->orderBy('title')->get();
        $this->page['groupsWeek'] = $queryGroupsWeek->orderBy('title')->get();
        $this->page['dayOfWeek'] = $data['date']->dayOfWeek;
        $this->page['time'] = $data['date']->toTimeString('minute');
        $this->page['date'] = $data['date']->translatedFormat('j F Y');
        $this->page['startOfWeek'] = $data['date']->startOfWeek()->translatedFormat('j F Y');
        $this->page['endOfWeek'] = $data['date']->endOfWeek()->translatedFormat('j F Y');
    }

    protected function processRequest(Request $request): array|RedirectResponse|Redirector
    {
        try {
            $data = $request->validate([
                'date' => 'sometimes|required|date_format:d.m.Y',
                'city' => 'sometimes|required|string|exists:naekb_groups,city',
                'location' => 'sometimes|required|string|exists:naekb_groups,location',
            ]);
        } catch (ValidationException $e) {
            $getParams = http_build_query($request->except(array_keys($e->getFields())));
            $currentUrl = url()->current();
            if (empty($getParams)) {
                return redirect()->to($currentUrl);
            }
            return redirect()->to("{$currentUrl}?{$getParams}");
        }

        $site = Site::getActiveSite();
        if (!empty($data['date'])) {
            $data['date'] = Carbon::createFromFormat('d.m.Y', $data['date']);
        } else {
            $data['date'] = Carbon::now();
        }
        $data['date']->setLocale($site->locale);

        return $data;
    }
}
