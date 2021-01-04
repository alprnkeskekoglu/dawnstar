<?php

namespace Dawnstar\Http\Controllers;

use Dawnstar\Models\Form;
use Dawnstar\Models\FormResult;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class FormResultController extends BaseController
{
    public function index(int $formId)
    {
        $form = Form::find($formId);

        if (is_null($form)) {
            return redirect()->route('dawnstar.form.index')->withErrors(__('DawnstarLang::form.response_message.id_error', ['id' => $formId]))->withInput();
        }

        $results = $form->results()->paginate(20);

        $breadcrumb = [
            [
                'name' => __('DawnstarLang::form.index_title'),
                'url' => route('dawnstar.form.index')
            ],
            [
                'name' => __('DawnstarLang::form.result_title'),
                'url' => '#'
            ]
        ];

        return view('DawnstarView::pages.form_results.index', compact('form', 'results', 'breadcrumb'));
    }

    public function updateReadStatus(Request $request)
    {
        $id = $request->get('id');

        $formResult = FormResult::find($id);

        if($formResult) {
            $formResult->update(['read' => 1]);
        }
    }


    private function getBreadcrumb(array $parameters)
    {
        $breadcrumb = [];

        foreach ($parameters as $param) {
            $breadcrumb[] = [
                'name' => __('DawnstarLang::form.' . $param[0] . '_title'),
                'url' => route('dawnstar.form.' . $param[1] ?? '', $param[2] ?? [])
            ];
        }

        return $breadcrumb;
    }
}
