@extends('layouts.app')

@section('main-content')
    <main class="main-documents">

        <div class="documents-container">
            <h3 class="documents-main-title">Все документы</h3>

                <div class="documents-table-wrapper">
                    <table class="documents-table">
                        <thead>
                        <tr>
                            <th class="col-name">Название</th>
                            <th class="col-date">Дата подписания</th>
                            <th class="col-actions"></th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($user_contracts as $contract)
                            <tr>
                                <!-- Название документа с иконкой -->
                                <td class="col-name">
                                    <div class="document-title-block">
                                        <span class="document-name">Договор об оказании платных услуг в СЦ «ТАИР»</span>
                                    </div>
                                </td>

                                <!-- Форматированная дата подписи -->
                                <td class="col-date">
                                    {{ \Carbon\Carbon::parse($contract->sign_date)->locale('ru')->isoFormat('D MMMM YYYY г.') }}
                                </td>

                                <!-- Кнопка действия (три точки) -->
                                <td class="col-actions">
                                    <button class="btn-more" title="Действия">
                                        <span></span>
                                        <span></span>
                                        <span></span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <div class="empty-documents-block">
                                <h2>Пока у вас нет подписанных договоров об оказании платных услуг</h2>
                                <p>Чтобы ознакомиться и подписать договор, нажмите кнопку ниже, он появится здесь после
                                    подписания</p>
                                <a class="button-to-sign-contract" href="{{ route('sign-contract') }}">К договору</a>
                            </div>
                            <tr>
                                <td colspan="3" class="no-documents">
                                    У вас пока нет подписанных договоров.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>


        </div>


    </main>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ url('frontend/css/style-documents.css')}}">
@endpush
