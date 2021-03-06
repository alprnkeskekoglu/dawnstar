<?php

namespace Dawnstar\Contracts\Services;

use Dawnstar\Contracts\Interfaces\ModelStoreInterface;
use Dawnstar\Models\Meta;
use Dawnstar\Models\Page;
use Dawnstar\Models\PageExtra;

class ModelStoreService implements ModelStoreInterface
{
    public function store($model, $data)
    {
        $data['admin_id'] = $data['admin_id'] ?? auth('admin')->id();

        return $model::create($data);
    }

    public function update($model, $data)
    {
        $model->update($data);
    }

    public function storeDetails($model, $details)
    {
        foreach ($details as $languageId => $detail) {

            if (isset($detail['status']) && $detail['status'] != 1) {
                continue;
            }

            $extras = $detail['extras'] ?? [];
            $medias = $detail['medias'] ?? [];
            unset($detail['extras'], $detail['medias']);

            $detail['slug'] = $detail['slug'] != '/' ? rtrim($detail['slug'], '/') : $detail['slug'];

            $pageDetail = $model->details()->updateOrCreate(
                [
                    'language_id' => $languageId
                ],
                $detail
            );

            $this->storeExtras($pageDetail, $extras);

            $this->storeMedias($pageDetail, $medias);
        }
    }

    public function storeExtras($model, $extras)
    {
        $model->extras()->delete();
        foreach ($extras as $key => $value) {
            $model->extras()->updateOrCreate([
                'key' => $key,
                'value' => $value,
            ]);
        }
    }

    public function storeMedias($model, $medias)
    {
        foreach ($medias as $key => $mediaIds) {

            if (is_null($mediaIds)) {
                $mediaIds = [];
            }

            $temp = [];

            $order = 0;
            foreach ($mediaIds as $mediaId) {
                $temp[$mediaId] = [
                    'model_type' => $model::class,
                    'model_id' => $model->id,
                    'media_key' => $key,
                    'media_id' => $mediaId,
                    'order' => ++$order
                ];
            }

            $model->medias()->wherePivot('media_key', $key)->sync($temp);
        }
    }

    public function storeMetas($model, $metas)
    {
        foreach ($metas as $languageId => $meta) {
            $detail = $model->details()->where('language_id', $languageId)->first();
            $url = $detail ? $detail->url : null;

            if($url) {
                foreach ($meta as $key => $value) {
                    Meta::updateOrCreate(
                        [
                            'url_id' => $url->id,
                            'key' => $key,
                        ],
                        [
                            'value' => $value
                        ]
                    );
                }
            }
        }
    }
}
