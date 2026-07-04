<x-filament-panels::page>

    {{-- Filtros --}}
    <x-filament::section>

        <x-slot name="heading">
            Buscar Cliente
        </x-slot>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            {{-- Código --}}
            <div>

                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Código
                </label>

                <input
                    type="text"
                    wire:model.live="codigo"
                    placeholder="Ej: ADUL-1"
                    class="
                        w-full
                        rounded-lg
                        border
                        border-gray-300
                        bg-white
                        px-3
                        py-2
                        text-sm
                        text-gray-900
                        placeholder-gray-400
                        shadow-sm
                        focus:border-primary-500
                        focus:ring-2
                        focus:ring-primary-500
                        dark:border-gray-700
                        dark:bg-gray-900
                        dark:text-white
                        dark:placeholder-gray-500
                    "
                >

            </div>

            {{-- Nombre --}}
            <div>

                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Nombre
                </label>

                <input
                    type="text"
                    wire:model.live="nombre"
                    placeholder="Nombre del cliente"
                    class="
                        w-full
                        rounded-lg
                        border
                        border-gray-300
                        bg-white
                        px-3
                        py-2
                        text-sm
                        text-gray-900
                        placeholder-gray-400
                        shadow-sm
                        focus:border-primary-500
                        focus:ring-2
                        focus:ring-primary-500
                        dark:border-gray-700
                        dark:bg-gray-900
                        dark:text-white
                        dark:placeholder-gray-500
                    "
                >

            </div>

            {{-- Estado --}}
            <div>

                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Estado General
                </label>

                <select
                    wire:model.live="estado"
                    class="
                        w-full
                        rounded-lg
                        border
                        border-gray-300
                        bg-white
                        px-3
                        py-2
                        text-sm
                        text-gray-900
                        shadow-sm
                        focus:border-primary-500
                        focus:ring-2
                        focus:ring-primary-500
                        dark:border-gray-700
                        dark:bg-gray-900
                        dark:text-white
                    "
                >

                    <option value="">Todos</option>

                    <option value="Pendiente">
                        Pendiente
                    </option>

                    <option value="En Proceso">
                        En Proceso
                    </option>

                    <option value="Decorado">
                        Decorado
                    </option>

                    <option value="Entregado">
                        Entregado
                    </option>

                </select>

            </div>

        </div>

    </x-filament::section>

    <br>

    {{-- Tabla --}}
    <x-filament::section>

        <x-slot name="heading">
            Clientes
        </x-slot>

        <div class="overflow-x-auto">

            <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">

                <thead>

                    <tr class="text-left">

                        <th class="py-3">Código</th>

                        <th>Nombre</th>

                        <th>Teléfono</th>

                        <th>Tipo</th>

                        <th>Estado</th>

                        <th>Registro</th>

                        <th class="text-center">
                            Acciones
                        </th>

                    </tr>

                </thead>

                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">

                @forelse($customers as $customer)

                    <tr>

                        <td class="py-3 font-semibold">
                            {{ $customer->CodigoCustomer }}
                        </td>

                        <td>
                            {{ $customer->Nombre }}
                        </td>

                        <td>
                            {{ $customer->Telefono }}
                        </td>

                        <td>

                            @if($customer->Tipo == 'Adulto')

                                <x-filament::badge color="primary">
                                    Adulto
                                </x-filament::badge>

                            @else

                                <x-filament::badge color="success">
                                    Niño
                                </x-filament::badge>

                            @endif

                        </td>

                        <td>

                            @switch($customer->EstadoGeneral)

                                @case('Pendiente')
                                    <x-filament::badge color="gray">
                                        Pendiente
                                    </x-filament::badge>
                                @break

                                @case('En Proceso')
                                    <x-filament::badge color="warning">
                                        En Proceso
                                    </x-filament::badge>
                                @break

                                @case('Decorado')
                                    <x-filament::badge color="info">
                                        Decorado
                                    </x-filament::badge>
                                @break

                                @case('Entregado')
                                    <x-filament::badge color="success">
                                        Entregado
                                    </x-filament::badge>
                                @break

                            @endswitch

                        </td>

                        <td>
                            {{ $customer->created_at->format('d/m/Y') }}
                        </td>

                        <td>

                            <div class="flex justify-center gap-2">

                                <x-filament::button
                                    size="sm"
                                    color="gray"
                                    wire:click="openModal({{ $customer->IdCustomer }}, false)"
                                >
                                    Ver
                                </x-filament::button>

                                <x-filament::button
                                    size="sm"
                                    color="warning"
                                    wire:click="openModal({{ $customer->IdCustomer }}, true)"
                                >
                                    Editar
                                </x-filament::button>

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="7"
                            class="text-center py-8 text-gray-500"
                        >
                            No se encontraron clientes.
                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

            @if($showModal)

            <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50">

                <div class="bg-white dark:bg-gray-900 w-[95%] max-w-6xl rounded-xl shadow-xl p-6 overflow-y-auto max-h-[90vh]">

                    {{-- HEADER --}}
                    <div class="flex justify-between items-center mb-4">

                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                            Cliente: {{ $customer?->CodigoCustomer }}
                        </h2>

                        <button wire:click="closeModal" class="text-gray-500 hover:text-red-500">
                            ✖
                        </button>

                    </div>

                    {{-- GRID 2x2 --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        {{-- 1. INFORMACIÓN CLIENTE --}}
                        <div class="bg-gray-100 dark:bg-gray-800 p-4 rounded-lg">

                            <h3 class="font-semibold mb-3 text-gray-900 dark:text-white">
                                Información del Cliente
                            </h3>

                            <p><b>Código:</b> {{ $customer->CodigoCustomer }}</p>
                            <p><b>Nombre:</b> {{ $customer->Nombre }}</p>
                            <p><b>Teléfono:</b> {{ $customer->Telefono }}</p>
                            <p><b>Sexo:</b> {{ $customer->Sexo }}</p>
                            <p><b>Tipo:</b> {{ $customer->Tipo }}</p>
                            <p><b>Registro:</b> {{ $customer->created_at }}</p>

                        </div>

                        {{-- 2. GESTIÓN PROCESO --}}
                        <div class="bg-gray-100 dark:bg-gray-800 p-4 rounded-lg">

                            <h3 class="font-semibold mb-3 text-gray-900 dark:text-white">
                                Gestión del Proceso
                            </h3>

                            <label class="text-sm">Estado General</label>

                            <select
                                wire:model="EstadoGeneral"
                                @if(!$editMode) disabled @endif
                                class="w-full mt-1 p-2 rounded border dark:bg-gray-900 dark:border-gray-700"
                            >
                                <option>Pendiente</option>
                                <option>En Proceso</option>
                                <option>Decorado</option>
                                <option>Entregado</option>
                            </select>

                            <label class="text-sm mt-3 block">Observación</label>

                            <textarea
                                wire:model="Observacion"
                                @if(!$editMode) disabled @endif
                                class="w-full mt-1 p-2 rounded border dark:bg-gray-900 dark:border-gray-700"
                                rows="4"
                            ></textarea>
                        </div>

                        {{-- AGENDAMIENTO --}}
                            <div class="bg-gray-100 dark:bg-gray-800 p-4 rounded-lg">

                                <h3 class="font-semibold mb-3 text-gray-900 dark:text-white">
                                    Agendamiento
                                </h3>

                                @if($hasAppointment)

                                    {{-- Fecha y hora desde Availability --}}
                                    <p>
                                        <b>Fecha:</b>
                                        {{ $appointment->availability?->AvailableDate }}
                                    </p>

                                    <p>
                                        <b>Hora:</b>
                                        {{ $appointment->availability?->AvailableTime }}
                                    </p>

                                    <p>
                                        <b>Estado cita:</b>
                                        {{ $appointment->Status }}
                                    </p>

                                    {{-- ASISTENCIA --}}
                                    <label class="text-sm mt-3 block">
                                        Estado de asistencia
                                    </label>

                                    <select
                                        wire:model="appointmentStatus"
                                        @if(!$editMode) disabled @endif
                                        class="w-full mt-1 p-2 rounded border dark:bg-gray-900 dark:border-gray-700"
                                    >
                                        <option value="">Seleccione</option>
                                        <option value="Asistio">Asistió</option>
                                        <option value="No Asistio">No asistió</option>
                                    </select>

                                    {{-- NOTAS --}}
                                    <label class="text-sm mt-3 block">
                                        Notas de asistencia
                                    </label>

                                    <textarea
                                        wire:model="appointmentNotes"
                                        @if(!$editMode) disabled @endif
                                        class="w-full mt-1 p-2 rounded border dark:bg-gray-900 dark:border-gray-700"
                                        rows="3"
                                    ></textarea>

                                @else

                                    <p class="text-gray-500">
                                        No hay agendamiento registrado.
                                    </p>

                                @endif

                            </div>

                        {{-- 4. HISTORIAL --}}
                        <div class="bg-gray-100 dark:bg-gray-800 p-4 rounded-lg max-h-[350px] overflow-y-auto">

                            <h3 class="font-semibold mb-3 text-gray-900 dark:text-white">
                                Historial
                            </h3>

                            @forelse($histories as $history)

                                <div class="border-b py-2">

                                    <p class="text-sm text-gray-500">
                                        {{ $history->created_at }}
                                    </p>

                                    <p><b>Estado:</b> {{ $history->Estado }}</p>

                                    <p class="text-sm">
                                        {{ $history->Observacion }}
                                    </p>

                                    <p class="text-xs text-gray-400">
                                        {{ $history->user?->name }}
                                    </p>

                                </div>

                            @empty
                                <p class="text-gray-500">Sin historial</p>
                            @endforelse

                        </div>                    
                    </div>
                    {{-- BOTÓN GUARDAR --}}
                        @if($editMode)
                            <div class="mt-6 border-t pt-4 flex justify-end">

                                <button
                                    wire:click="save"
                                    class="bg-amber-500 hover:bg-amber-600 text-white font-semibold px-6 py-3 rounded-lg shadow-md transition"
                                >
                                    Guardar Cambios
                                </button>

                            </div>
                        @endif
                </div>              
            </div>
            @endif
        </div>

    </x-filament::section>

</x-filament-panels::page>