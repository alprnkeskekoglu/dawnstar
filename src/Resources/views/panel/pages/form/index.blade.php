@extends('DawnstarView::layouts.app')


@section('content')
    <main id="main-container">

        <div class="content content-max-width">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill font-size-h2 font-w400 mt-2 mb-0 mb-sm-2">{{ __('DawnstarLang::form.index_title') }}</h1>
                @include('DawnstarView::layouts.breadcrumb')
            </div>
        </div>

        <div class="content">
            <div class="block block-rounded">
                <div class="block-content">
                    @include('DawnstarView::layouts.alerts')

                    <div class="row items-push justify-content-end text-right">
                        <div class="mr-2">
                            <a href="{{ route('dawnstar.forms.create') }}" class="btn btn-sm btn-primary" data-toggle="click-ripple">
                                <i class="fa fa-fw fa-plus mr-1"></i>
                                {{ __('DawnstarLang::general.add_new') }}
                            </a>
                        </div>
                    </div>

                    <table class="table table-striped table-vcenter">
                        <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">#</th>
                            <th class="text-center">{{ __('DawnstarLang::form.status') }}</th>
                            <th>{{ __('DawnstarLang::form.name') }}</th>
                            <th class="text-center">{{ __('DawnstarLang::form.result_count') }}</th>
                            <th class="text-center" style="width: 100px;">{{ __('DawnstarLang::general.actions') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($forms as $form)
                            @php
                                $resultCount = $form->results->count();
                                $unreadResultCount = $form->unread_result_count;
                            @endphp
                            <tr>
                                <th class="text-center" scope="row">
                                    {{ $form->id }}
                                </th>
                                <td class="text-center">
                                    <span class="badge badge-{{ getStatusColorClass($form->status) }} fa-1x p-2">
                                        {{ getStatusText($form->status) }}
                                    </span>
                                </td>
                                <td class="font-w600 fa-1x">
                                    {{ $form->name }}
                                </td>
                                <td class="text-center">
                                    {{ $resultCount }} /
                                    <span class="badge badge-pill {{ $unreadResultCount == 0 ? '' : 'badge-success' }} p-1 fa-1x">
                                        {{ $unreadResultCount }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="{{ route('dawnstar.forms.edit', ['id' => $form->id]) }}" class="btn btn-sm btn-primary" data-toggle="tooltip" data-placement="bottom" title="{{ __('DawnstarLang::general.edit') }}">
                                            <i class="fa fa-pencil-alt"></i>
                                        </a>

                                        <button type="button" class="js-swal-confirm btn btn-sm btn-danger" data-toggle="tooltip" data-placement="bottom" data-url="{{ route('dawnstar.forms.destroy', ['id' => $form->id]) }}" title="{{ __('DawnstarLang::general.delete') }}">
                                            <i class="fa fa-times"></i>
                                        </button>

                                        @if($form->results->isNotEmpty())
                                            <a href="{{ route('dawnstar.forms.results.index', ['formId' => $form->id]) }}" class="btn btn-sm btn-secondary" data-toggle="tooltip" data-placement="bottom" title="{{ __('DawnstarLang::form.result_title') }}">
                                                <i class="fa fa-comments"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ dawnstarAsset('plugins/sweetalert2/sweetalert2.min.css') }}">
@endpush

@push('scripts')
    <script src="{{ dawnstarAsset('plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script>
        jQuery('.js-swal-confirm').on('click', e => {
            var url = e.currentTarget.getAttribute('data-url');
            swal.fire({
                title: '{{ __('DawnstarLang::general.swal.title') }}',
                text: '{{ __('DawnstarLang::general.swal.subtitle') }}',
                icon: 'warning',
                showCancelButton: true,
                customClass: {
                    confirmButton: 'btn btn-danger m-1',
                    cancelButton: 'btn btn-secondary m-1'
                },
                confirmButtonText: '{{ __('DawnstarLang::general.swal.confirm_btn') }}',
                cancelButtonText: '{{ __('DawnstarLang::general.swal.cancel_btn') }}',
                html: false,
                preConfirm: e => {
                    return new Promise(resolve => {
                        setTimeout(() => {
                            resolve();
                        }, 50);
                    });
                }
            }).then(result => {
                if (result.value) {
                    $.ajax({
                        'url': url,
                        'method': 'DELETE',
                        'data': {'_token': '{{ csrf_token() }}'},
                        success: function (response) {
                            swal.fire('{{ __('DawnstarLang::general.swal.success.title') }}', '{{ __('DawnstarLang::general.swal.success.subtitle') }}', 'success');
                            setTimeout(function () {
                                location.reload();
                            }, 1000);
                        },
                        error: function (response) {
                            swal.fire('{{ __('DawnstarLang::general.swal.error.title') }}', '{{ __('DawnstarLang::general.swal.error.subtitle') }}', 'error');
                        }
                    })
                }
            });
        });
    </script>
@endpush
