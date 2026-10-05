<div>

    <style>
        body[data-theme="dark"] .flow-card{
            background:#1F2937 !important;
            color:white !important;
        }
        body[data-theme="dark"] .flow-card input,
        body[data-theme="dark"] .flow-card select{
            background:#374151;
            color:white;
            border:1px solid #4B5563;
        }
        body[data-theme="dark"] .flow-card input::placeholder{
            color:#D1D5DB;
        }
        body[data-theme="dark"] .flow-modal{
            background:#1F2937 !important;
            color:white;
        }
        body[data-theme="dark"] .flow-close{
            background:#4B5563 !important;
            color: white !important;
        }
        body[data-theme="dark"] label{
            color:white;
        }
        body[data-theme="dark"] .flow-card,
        body[data-theme="dark"] .flow-modal{
            color:#F9FAFB !important;
        }
        body[data-theme="dark"] .flow-card h1,
        body[data-theme="dark"] .flow-card h2,
        body[data-theme="dark"] .flow-card h3,
        body[data-theme="dark"] .flow-card p,
        body[data-theme="dark"] .flow-card label{
            color:#F9FAFB !important;
        }
        body[data-theme="dark"] .flow-modal h1,
        body[data-theme="dark"] .flow-modal h2,
        body[data-theme="dark"] .flow-modal h3,
        body[data-theme="dark"] .flow-modal p{
            color:#F9FAFB !important;
        }
        .flow-modal-title{
            color:#111827;
            margin-bottom:15px;
        }
        body[data-theme="dark"] .flow-modal-title{
            color:#F9FAFB;
        }
        .flow-btn {
            background:#F59E0B;
            color:white;
            transition:.3s;
        }
        .flow-btn:hover {
            background:#D97706;
        }
        .flow-info-box {
            background: #fffbeb;
            border: 1px solid #fde68a;
            color: #92400e;
        }
        body[data-theme="dark"] .flow-info-box {
            background: #374151;
            border: 1px solid #4B5563;
            color: #F9FAFB;
        }
        .flow-select-btn {
            background: white;
            color: inherit;
            border: 1px solid #d1d5db;
        }
        .flow-select-btn.active {
            background: #F59E0B !important;
            color: white !important;
            border-color: #F59E0B !important;
        }
        body[data-theme="dark"] .flow-select-btn:not(.active) {
            background: #374151;
            color: white;
            border-color: #4B5563;
        }
    </style>

    <div
        class="flow-card"
        style="
            background:white;
            padding:30px;
            border-radius:15px;
            box-shadow:0 10px 30px rgba(0,0,0,.12);
            transition:.3s;
            max-width: 800px;
            margin: 0 auto;
        "
    >
        <h2 style="margin-bottom:10px;text-align:center; font-size: 1.5rem; font-weight: bold;">
            FlowAccess
        </h2>
        <p style="text-align:center; margin-bottom: 25px; color: #6b7280;">
            {{ __('appointment.portal_title') }}
        </p>

        {{-- SUCCESS --}}
        @if (session()->has('success'))
            <div style="margin-bottom: 20px; padding: 15px; border-radius: 10px; background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0;">
                {{ session('success') }}
            </div>
        @endif

        {{-- ================= VALIDACIÓN ================= --}}
        @if (!$mostrarAgenda)
            <div style="text-align: center;">
                <h3 style="font-size: 1.25rem; font-weight: bold; margin-bottom: 10px;">{{ __('appointment.welcome') }}</h3>
                <p style="color: #6b7280; margin-bottom: 20px;">{{ __('appointment.enter_code') }}</p>

                <input
                    type="text"
                    wire:model="codigo"
                    placeholder="{{ __('appointment.placeholder_code') }}"
                    style="
                        width:100%;
                        padding:12px;
                        border-radius:10px;
                        border:1px solid #d1d5db;
                        margin-bottom:10px;
                        box-sizing:border-box;
                    "
                >

                @error('codigo')
                    <small style="color:#ef4444; display: block; text-align: left; margin-bottom: 10px;">{{ $message }}</small>
                @enderror

                <button
                    wire:click="validarCodigo"
                    class="flow-btn"
                    style="
                        width:100%;
                        padding:14px;
                        border:none;
                        border-radius:10px;
                        font-weight:bold;
                        cursor:pointer;
                    "
                >
                    {{ __('appointment.validate_btn') }}
                </button>
            </div>
        @endif

        {{-- ================= AGENDA ================= --}}
        @if ($mostrarAgenda && $customer)
            {{-- INFO CLIENTE --}}
            <div class="flow-info-box" style="margin-bottom: 20px; padding: 20px; border-radius: 10px;">
                <h3 style="font-weight: bold; margin-bottom: 15px;">{{ __('appointment.your_info') }}</h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; font-size: 0.875rem;">
                    <div><b>{{ __('appointment.name') }}:</b><br>{{ $customer->Nombre }}</div>
                    <div><b>{{ __('appointment.phone') }}:</b><br>{{ $customer->Telefono }}</div>
                    <div><b>{{ __('appointment.code') }}:</b><br>{{ $customer->CodigoCustomer }}</div>
                    <div><b>{{ __('appointment.type') }}:</b><br>{{ $customer->Tipo }}</div>
                </div>
            </div>

            {{-- FECHAS --}}
            <div style="margin-bottom: 20px;">
                <h3 style="font-weight: bold; margin-bottom: 15px;">{{ __('appointment.select_date') }}</h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(80px, 1fr)); gap: 10px;">
                    @foreach($fechas as $fecha)
                        <button
                            wire:click="$set('fechaSeleccionada','{{ $fecha->AvailableDate }}')"
                            class="flow-select-btn {{ $fechaSeleccionada == $fecha->AvailableDate ? 'active' : '' }}"
                            style="
                                padding: 12px;
                                border-radius: 10px;
                                cursor: pointer;
                                transition: .2s;
                                display: flex;
                                flex-direction: column;
                                align-items: center;
                                justify-content: center;
                            "
                        >
                            <span style="font-weight: bold;">{{ \Carbon\Carbon::parse($fecha->AvailableDate)->format('d M') }}</span>
                            <span style="font-size: 0.75rem; margin-top: 4px; opacity: 0.8;">{{ ucfirst(\Carbon\Carbon::parse($fecha->AvailableDate)->translatedFormat('l')) }}</span>
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- HORARIOS --}}
            @if($fechaSeleccionada)
                <div style="margin-bottom: 25px;">
                    <h3 style="font-weight: bold; margin-bottom: 15px;">{{ __('appointment.select_time') }}</h3>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(80px, 1fr)); gap: 10px;">
                        @foreach($horarios as $horario)
                            <button
                                wire:click="$set('horarioSeleccionado','{{ $horario->IdAvailability }}')"
                                class="flow-select-btn {{ $horarioSeleccionado == $horario->IdAvailability ? 'active' : '' }}"
                                style="
                                    padding: 12px;
                                    border-radius: 10px;
                                    cursor: pointer;
                                    transition: .2s;
                                "
                            >
                                {{ \Carbon\Carbon::parse($horario->AvailableTime)->format('H:i') }}
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- BOTÓN --}}
            @if($horarioSeleccionado)
                <button
                    wire:click="guardarCita"
                    class="flow-btn"
                    style="
                        width:100%;
                        padding:14px;
                        border:none;
                        border-radius:10px;
                        font-weight:bold;
                        cursor:pointer;
                        font-size: 1.125rem;
                    "
                >
                    {{ __('appointment.confirm_btn') }}
                </button>
            @endif
        @endif

    </div>

    @if($showModal)
        <div
            style="
                position:fixed;
                inset:0;
                background:rgba(0,0,0,.7);
                display:flex;
                align-items:center;
                justify-content:center;
                z-index:999999;
            "
        >
        <div
            class="flow-modal"
            style="
                width: 100%;
                max-width: 400px;
                background:white;
                border-radius:15px;
                padding:30px;
                text-align:center;
                transition:.3s;
                margin: 0 20px;
            "
        >
            <div style="width: 60px; height: 60px; background: #d1fae5; color: #059669; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px; margin: 0 auto 15px;">
                ✔
            </div>

            <h2 class="flow-modal-title" style="font-size: 1.25rem; font-weight: bold;">
                {{ __('appointment.confirmed_title') }}
            </h2>

            <div style="background: rgba(245, 158, 11, 0.1); border: 1px solid #F59E0B; border-radius: 10px; padding: 15px; margin-bottom: 20px; text-align: left;">
                <p style="margin-bottom: 5px;"><b>{{ __('appointment.name') }}:</b> <span>{{ $confirmData['nombre'] ?? '' }}</span></p>
                <p style="margin-bottom: 5px;"><b>{{ __('appointment.code') }}:</b> <span>{{ $confirmData['codigo'] ?? '' }}</span></p>
                <p style="margin-bottom: 5px;"><b>{{ __('appointment.date') }}:</b> <span>{{ $confirmData['fecha'] ?? '' }}</span></p>
                <p style="margin-bottom: 0;"><b>{{ __('appointment.time') }}:</b> <span>{{ $confirmData['hora'] ?? '' }}</span></p>
            </div>

            {{-- ADVERTENCIA COMPROBANTE --}}
            <div style="background: #fee2e2; border: 1px solid #f87171; border-radius: 10px; padding: 12px; margin-bottom: 20px; color: #b91c1c; font-size: 0.875rem;">
                <strong>{{ __('appointment.warning_title') }}</strong><br>
                {{ __('appointment.warning_text') }}
            </div>

            <div style="display: flex; flex-direction: column; gap: 10px;">
                <a
                    style="display: block; padding: 12px; background: #25D366; color: white; border-radius: 10px; text-decoration: none; font-weight: bold;"
                    href="https://wa.me/?text=📅 Cita confirmada%0ACliente: {{ urlencode($confirmData['nombre'] ?? '') }}%0ACódigo: {{ urlencode($confirmData['codigo'] ?? '') }}%0AFecha: {{ urlencode($confirmData['fecha'] ?? '') }}%0AHora: {{ urlencode($confirmData['hora'] ?? '') }}"
                    target="_blank"
                >
                    {{ __('appointment.share_wa') }}
                </a>

                <a
                    style="display: block; padding: 12px; background: #EA4335; color: white; border-radius: 10px; text-decoration: none; font-weight: bold;"
                    href="mailto:?subject=Confirmación de Cita - FlowAccess&body=Hola, mi cita ha sido confirmada.%0A%0ANombre: {{ urlencode($confirmData['nombre'] ?? '') }}%0ACódigo: {{ urlencode($confirmData['codigo'] ?? '') }}%0AFecha: {{ urlencode($confirmData['fecha'] ?? '') }}%0AHora: {{ urlencode($confirmData['hora'] ?? '') }}"
                >
                    {{ __('appointment.share_email') }}
                </a>

                <button
                    onclick="window.print()"
                    style="width: 100%; padding: 12px; background: #374151; color: white; border: none; border-radius: 10px; font-weight: bold; cursor: pointer;"
                >
                    {{ __('appointment.print') }}
                </button>

                <button
                    wire:click="$set('showModal', false)"
                    class="flow-close"
                    style="width: 100%; padding: 12px; background: #f3f4f6; color: #374151; border: none; border-radius: 10px; cursor: pointer; margin-top: 5px;"
                >
                    {{ __('appointment.close') }}
                </button>
            </div>
        </div>
    </div>
    @endif
    @if($showAlreadyScheduledModal)
        <div
            style="
                position:fixed;
                inset:0;
                background:rgba(0,0,0,.75);
                display:flex;
                align-items:center;
                justify-content:center;
                z-index:999999;
                padding:20px;
            "
        >
            <div
                class="flow-modal"
                style="
                    width:100%;
                    max-width:500px;
                    background:white;
                    border-radius:18px;
                    padding:35px;
                    text-align:center;
                    box-shadow:0 20px 50px rgba(0,0,0,.25);
                "
            >

                {{-- ICONO --}}
                <div
                    style="
                        width:75px;
                        height:75px;
                        background:#fef3c7;
                        color:#d97706;
                        border-radius:50%;
                        display:flex;
                        align-items:center;
                        justify-content:center;
                        font-size:34px;
                        margin:0 auto 20px;
                    "
                >
                    !
                </div>

                {{-- TITULO --}}
                <h2
                    class="flow-modal-title"
                    style="
                        font-size:1.6rem;
                        font-weight:bold;
                        margin-bottom:15px;
                    "
                >
                    {{ __('appointment.already_scheduled_title') }}
                </h2>

                {{-- MENSAJE --}}
                <p
                    style="
                        font-size:1.1rem;
                        line-height:1.6;
                        color:#6b7280;
                        margin-bottom:25px;
                    "
                >
                    {{ __('appointment.already_scheduled_message') }}
                </p>

                {{-- BOTÓN --}}
                <button
                    wire:click="$set('showAlreadyScheduledModal', false)"
                    class="flow-btn"
                    style="
                        width:100%;
                        padding:14px;
                        border:none;
                        border-radius:10px;
                        font-weight:bold;
                        font-size:1rem;
                        cursor:pointer;
                    "
                >
                    {{ __('appointment.close') }}
                </button>

            </div>
        </div>
    @endif
</div>