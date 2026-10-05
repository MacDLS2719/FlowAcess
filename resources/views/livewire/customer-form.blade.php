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
            body[data-theme="dark"] .flow-modal p{
                color:#F9FAFB !important;
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

            body[data-theme="dark"] .flow-card select option{
                background:#374151;
                color:white;
            }
        .flow-modal-title{
            color:#111827;
            margin-bottom:15px;
        }

        body[data-theme="dark"] .flow-modal-title{
            color:#F9FAFB;
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
        "
    >

        <h2 style="margin-bottom:25px;text-align:center;">
            {{ __('customer.register') }}
        </h2>

        <form wire:submit.prevent="save">

            {{-- Nombre --}}
            <div style="margin-bottom:15px;text-align:left;">

                <label>{{ __('customer.name') }}</label>

                <input
                    type="text"
                    wire:model="Nombre"
                    placeholder="{{ __('customer.name') }}"
                    style="
                        width:100%;
                        padding:12px;
                        border-radius:10px;
                        border:1px solid #d1d5db;
                        margin-top:5px;
                        box-sizing:border-box;
                    "
                >

                @error('Nombre')
                    <small style="color:#ef4444;">{{ $message }}</small>
                @enderror

            </div>

            {{-- Teléfono --}}
            <div style="margin-bottom:15px;text-align:left;">

                <label>{{ __('customer.phone') }}</label>

                <input
                    type="text"
                    wire:model="Telefono"
                    placeholder="+57 300 123 4567"
                    style="
                        width:100%;
                        padding:12px;
                        border-radius:10px;
                        border:1px solid #d1d5db;
                        margin-top:5px;
                        box-sizing:border-box;
                    "
                >

                @error('Telefono')
                    <small style="color:#ef4444;">{{ $message }}</small>
                @enderror

            </div>

            {{-- Sexo --}}
            <div style="margin-bottom:15px;text-align:left;">

                <label>{{ __('customer.gender') }}</label>

                <select
                    wire:model="Sexo"
                    style="
                        width:100%;
                        padding:12px;
                        border-radius:10px;
                        border:1px solid #d1d5db;
                        margin-top:5px;
                        box-sizing:border-box;
                    "
                >
                    <option value="">{{ __('customer.select') }}</option>
                    <option value="Masculino">{{ __('customer.male') }}</option>
                    <option value="Femenino">{{ __('customer.female') }}</option>
                </select>

                @error('Sexo')
                    <small style="color:#ef4444;">{{ $message }}</small>
                @enderror

            </div>

            {{-- Tipo --}}
            <div style="margin-bottom:15px;text-align:left;">

                <label>{{ __('customer.type') }}</label>

                <select
                    wire:model="Tipo"
                    style="
                        width:100%;
                        padding:12px;
                        border-radius:10px;
                        border:1px solid #d1d5db;
                        margin-top:5px;
                        box-sizing:border-box;
                    "
                >
                    <option value="">{{ __('customer.select') }}</option>
                    <option value="Adulto">{{ __('customer.adult') }}</option>
                    <option value="Niño">{{ __('customer.child') }}</option>
                    <option value="Carnaval">{{ __('customer.carnival') }}</option>
                    <option value="Personalizado">{{ __('customer.custom') }}</option>
                </select>

                @error('Tipo')
                    <small style="color:#ef4444;">{{ $message }}</small>
                @enderror

            </div>

            {{-- Estado Contable --}}
            <div style="margin-bottom:25px;text-align:left;">

                <label>{{ __('customer.payment') }}</label>

                <select
                    wire:model="EstadoContable"
                    style="
                        width:100%;
                        padding:12px;
                        border-radius:10px;
                        border:1px solid #d1d5db;
                        margin-top:5px;
                        box-sizing:border-box;
                    "
                >
                    <option value="">{{ __('customer.select') }}</option>
                    <option value="Pago">{{ __('customer.paid') }}</option>
                    <option value="Debe">{{ __('customer.pending') }}</option>
                </select>

                @error('EstadoContable')
                    <small style="color:#ef4444;">{{ $message }}</small>
                @enderror

            </div>

            <button
                type="submit"
                style="
                    width:100%;
                    padding:14px;
                    background:#F59E0B;
                    color:white;
                    border:none;
                    border-radius:10px;
                    font-weight:bold;
                    cursor:pointer;
                    transition:.3s;
                "
                onmouseover="this.style.background='#D97706'"
                onmouseout="this.style.background='#F59E0B'"
            >
                {{ __('customer.save') }}
            </button>

        </form>

    </div>

    {{-- Modal --}}
    @if($showModal)

        <div
            style="
                position:fixed;
                inset:0;
                background:rgba(0,0,0,.6);
                display:flex;
                align-items:center;
                justify-content:center;
                z-index:9999;
            "
        >

            <div
                class="flow-modal"
                style="
                    width:360px;
                    background:white;
                    border-radius:15px;
                    padding:30px;
                    text-align:center;
                    transition:.3s;
                "
            >

                <h2 class="flow-modal-title">
                    {{ __('customer.success') }}
                </h2>

                <p>{{ __('customer.code') }}</p>

                <h1 style="color:#F59E0B;">
                    {{ $codigoGenerado }}
                </h1>

                <a
                    href="{{ route('customer.pdf',$customerRegistrado->IdCustomer) }}"
                    target="_blank"
                    style="
                        display:block;
                        margin-top:20px;
                        padding:12px;
                        background:#F59E0B;
                        color:white;
                        border-radius:10px;
                        text-decoration:none;
                    "
                >
                    📄 {{ __('customer.download') }}
                </a>

                <button
                    wire:click="$set('showModal',false)"
                    class="flow-close"
                    style="
                        width:100%;
                        margin-top:15px;
                        padding:12px;
                        border:none;
                        border-radius:10px;
                        background:#374151;
                        color:white;
                        cursor:pointer;
                    "
                >
                    {{ __('customer.close') }}
                </button>

            </div>

        </div>

    @endif

</div>