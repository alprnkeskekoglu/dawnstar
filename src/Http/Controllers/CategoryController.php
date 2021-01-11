<?php

namespace Dawnstar\Http\Controllers;

use Dawnstar\Foundation\FormBuilder;
use Dawnstar\Models\Category;
use Dawnstar\Models\CategoryDetail;
use Dawnstar\Models\CategoryDetailExtra;
use Dawnstar\Models\Container;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class CategoryController extends BaseController
{
    public function index(int $containerId)
    {
        $container = Container::findOrFail($containerId);

        $languages = $container->languages();

        $categories = $container->categories()
            ->where('parent_id', 0)
            ->orderBy('lft')
            ->with(['children' => function ($q) {
                $q->with(['children' => function ($que) {
                    $que->orderBy('lft');
                }])
                    ->orderBy('lft');
            }])
            ->get();

        $breadcrumb = [
            [
                'name' => __('DawnstarLang::category.index_title'),
                'url' => route('dawnstar.page.index', ['containerId' => $containerId])
            ],
            [
                'name' => __('DawnstarLang::category.create_title'),
                'url' => '#'
            ]
        ];

        return view('DawnstarView::pages.category.index', compact('container', 'categories', 'languages', 'breadcrumb'));
    }

    public function create(int $containerId)
    {
        $container = Container::findOrFail($containerId);

        $languages = $container->languages();
        $formBuilder = new FormBuilder('category', $containerId);

        $breadcrumb = [
            [
                'name' => __('DawnstarLang::category.index_title'),
                'url' => route('dawnstar.category.index', ['containerId' => $containerId])
            ],
            [
                'name' => __('DawnstarLang::category.create_title'),
                'url' => '#'
            ]
        ];

        return view('DawnstarView::pages.category.create', compact('container', 'formBuilder', 'languages', 'breadcrumb'));
    }

    public function store(Request $request, int $containerId)
    {
        $container = Container::findOrFail($containerId);
        $data = $request->except('_token');

        $details = $data['details'] ?? [];
        unset($data['details']);

        $data['container_id'] = $containerId;

        $category = Category::firstOrCreate($data);

        foreach ($details as $languageId => $detail) {

            $extras = $detail['extras'] ?? [];
            unset($detail['extras']);

            $categoryDetail = CategoryDetail::updateOrCreate(
                [
                    'category_id' => $category->id,
                    'language_id' => $languageId
                ],
                $detail
            );

            $categoryDetail->extras()->delete();
            foreach ($extras as $key => $value) {
                CategoryDetailExtra::firstOrCreate([
                    'category_detail_id' => $pageDetail->id,
                    'key' => $key,
                    'value' => $value,
                ]);
            }
        }

        // Admin Action
        addAction($category, 'store');

        return redirect()->route('dawnstar.category.index', ['containerId' => $containerId])->with('success_message', __('DawnstarLang::page.response_message.store'));
    }

    public function edit(int $containerId, int $id)
    {
        $container = Container::findOrFail($containerId);
        $category = Category::findOrFail($id);

        $languages = $container->languages();
        $formBuilder = new FormBuilder('category', $containerId, $id);

        $breadcrumb = [
            [
                'name' => __('DawnstarLang::category.index_title'),
                'url' => route('dawnstar.category.index', ['containerId' => $containerId])
            ],
            [
                'name' => __('DawnstarLang::category.edit_title'),
                'url' => '#'
            ]
        ];

        return view('DawnstarView::pages.category.edit', compact('container', 'category', 'formBuilder', 'languages', 'breadcrumb'));
    }

    public function update(Request $request, int $containerId, int $id)
    {
        $container = Container::findOrFail($containerId);
        $category = Category::findOrFail($id);

        $data = $request->except('_token');

        $details = $data['details'] ?? [];
        unset($data['details']);

        $category->update($data);

        foreach ($details as $languageId => $detail) {

            $extras = $detail['extras'] ?? [];
            unset($detail['extras']);

            $categoryDetail = CategoryDetail::updateOrCreate(
                [
                    'category_id' => $category->id,
                    'language_id' => $languageId
                ],
                $detail
            );

            $categoryDetail->extras()->delete();
            foreach ($extras as $key => $value) {
                CategoryDetailExtra::firstOrCreate([
                    'category_detail_id' => $categoryDetail->id,
                    'key' => $key,
                    'value' => $value,
                ]);
            }
        }

        // Admin Action
        addAction($category, 'update');

        return redirect()->route('dawnstar.category.edit', ['containerId' => $containerId, 'id' => $id])->with('success_message', __('DawnstarLang::page.response_message.update'));
    }

    public function delete(int $containerId, int $id)
    {
        $container = Container::find($containerId);
        if (is_null($container)) {
            return response()->json(['title' => __('DawnstarLang::general.swal.error.title'), 'subtitle' => __('DawnstarLang::general.swal.error.subtitle')], 406);
        }
        
        $category = Category::find($id);
        if (is_null($category)) {
            return response()->json(['title' => __('DawnstarLang::general.swal.error.title'), 'subtitle' => __('DawnstarLang::general.swal.error.subtitle')], 406);
        }

        $category->delete();

        // Admin Action
        addAction($category, 'delete');

        return response()->json(['title' => __('DawnstarLang::general.swal.success.title'), 'subtitle' => __('DawnstarLang::general.swal.success.subtitle')]);
    }

    public function saveOrder(Request $request, int $containerId)
    {
        $container = Container::findOrFail($containerId);

        $data = $request->get('data');

        $orderedData = $this->buildTree($data);

        foreach ($orderedData as $ordered) {
            $category = Category::find($ordered['id']);

            if($category) {
                unset($ordered['id']);

                $category->update($ordered);
            }
        }

        // Admin Action
        addAction($category, 'saveOrder');
    }

    public function buildTree(array $elements, $parentId = 0, $max = 0)
    {
        $branch = array();
        foreach ($elements as $element)
        {
            $element['lft'] = $max = $max + 1;
            $element['rgt'] = $max + 1;
            $element['parent_id'] = $parentId;

            if (isset($element['children']))
            {
                $children = $this->buildTree($element['children'], $element['id'], $max);
                if ($children)
                {

                    $element['rgt'] = $max = (isset(end($children)['rgt']) ? end($children)['rgt'] : 1) + 1;
                    $element['children'] = $children;
                } else
                {
                    $element['rgt'] = $max = $max + 1;
                }
            }

            $branch[] = $element;
        }

        return $this->unBuildTree($branch);
    }

    public function unBuildTree($elements, $branch = [])
    {
        foreach ($elements as $element)
        {
            if (isset($element['children']))
            {
                $branch = $this->unBuildTree($element['children'], $branch);
                unset($element['children']);
            }
            $branch[] = $element;
        }
        return $branch;
    }
}
