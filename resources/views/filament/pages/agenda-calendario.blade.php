<x-filament-panels::page>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 dark:bg-gray-900 dark:border-gray-800">

        {{-- ================= HEADER ================= --}}
        <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-800">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white capitalize">
                {{ $monthName }}
            </h2>

            <div class="flex items-center space-x-2">
                <button wire:click="prevMonth"
                    class="px-3 py-1 text-sm font-medium border rounded-md hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-200 dark:border-gray-700 dark:hover:bg-gray-700">
                    ← Anterior
                </button>

                <button wire:click="goToToday"
                    class="px-3 py-1 text-sm font-medium border rounded-md hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-200 dark:border-gray-700 dark:hover:bg-gray-700">
                    Hoy
                </button>

                <button wire:click="nextMonth"
                    class="px-3 py-1 text-sm font-medium border rounded-md hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-200 dark:border-gray-700 dark:hover:bg-gray-700">
                    Siguiente →
                </button>
            </div>
        </div>

        {{-- ================= CALENDARIO ================= --}}
        <div class="p-4">
            <div class="grid grid-cols-7 gap-px bg-gray-200 dark:bg-gray-700 rounded-lg overflow-hidden border">

                {{-- Días semana --}}
                @foreach(['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'] as $day)
                    <div class="bg-gray-50 dark:bg-gray-800 py-2 text-center text-xs font-semibold uppercase">
                        {{ $day }}
                    </div>
                @endforeach

                {{-- Espacios vacíos inicio --}}
                @for($i = 0; $i < $firstDayOfWeek; $i++)
                    <div class="bg-white dark:bg-gray-900 min-h-[120px] p-2"></div>
                @endfor

                {{-- Días del mes --}}
                @for($day = 1; $day <= $daysInMonth; $day++)
                    <div class="bg-white dark:bg-gray-900 min-h-[120px] p-2 flex flex-col hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">

                        {{-- número día --}}
                        <div class="flex justify-end">
                            <span class="text-sm font-medium w-7 h-7 flex items-center justify-center rounded-full
                                {{ ($day == now()->day && $currentMonth == now()->month && $currentYear == now()->year)
                                    ? 'bg-primary-600 text-white'
                                    : 'text-gray-900 dark:text-gray-100' }}">
                                {{ $day }}
                            </span>
                        </div>

                        {{-- citas --}}
                        <div class="mt-2 flex-1 overflow-y-auto space-y-1">

                            @if(isset($availabilitiesByDay[$day]))
                                @foreach($availabilitiesByDay[$day] as $avail)

                                    @php
                                        $time = \Carbon\Carbon::parse($avail->AvailableTime)->format('H:i');
                                        $appointment = $avail->appointments->first();
                                    @endphp

                                    @if($avail->Status == 0)
                                        {{-- DISPONIBLE --}}
                                        <div class="text-xs px-2 py-1 rounded bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400 truncate">
                                            🟢 {{ $time }}
                                        </div>
                                    @else

                                        {{-- AGENDADO (CLICK ABRE TU MODAL NUEVO) --}}
                                        @if($appointment && $appointment->customer)

                                            <button
                                                wire:click="openModal({{ $appointment->customer->IdCustomer }}, true)"
                                                type="button"
                                                class="w-full text-left text-xs px-2 py-1 rounded bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 border truncate hover:bg-red-200 dark:hover:bg-red-800 transition"
                                            >
                                                🔴 {{ $time }}
                                            </button>

                                        @else
                                            <div class="text-xs px-2 py-1 rounded bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 opacity-80">
                                                🔴 {{ $time }}
                                            </div>
                                        @endif

                                    @endif

                                @endforeach
                            @endif

                        </div>
                    </div>
                @endfor

                {{-- Espacios fin mes --}}
                @php
                    $totalCells = $firstDayOfWeek + $daysInMonth;
                    $remaining = $totalCells % 7 == 0 ? 0 : 7 - ($totalCells % 7);
                @endphp

                @for($i = 0; $i < $remaining; $i++)
                    <div class="bg-white dark:bg-gray-900 min-h-[120px] p-2"></div>
                @endfor

            </div>
        </div>
    </div>

    {{-- ================= MODAL ================= --}}
    @if($showModal)
        <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50">

            <div class="bg-white dark:bg-gray-900 w-[95%] max-w-6xl rounded-xl shadow-xl p-6 overflow-y-auto max-h-[90vh]">

                {{-- HEADER MODAL --}}
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                        Cliente: {{ $customer?->CodigoCustomer }}
                    </h2>

                    <button wire:click="closeModal" class="text-gray-500 hover:text-red-500 text-xl">
                        ✖
                    </button>
                </div>

                {{-- GRID --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    {{-- CLIENTE --}}
                    <div class="bg-gray-100 dark:bg-gray-800 p-4 rounded-lg">
                        <h3 class="font-semibold mb-3">Información del Cliente</h3>

                        <p><b>Código:</b> {{ $customer->CodigoCustomer }}</p>
                        <p><b>Nombre:</b> {{ $customer->Nombre }}</p>
                        <p><b>Teléfono:</b> {{ $customer->Telefono }}</p>
                        <p><b>Sexo:</b> {{ $customer->Sexo }}</p>
                    </div>

                    {{-- PROCESO --}}
                    <div class="bg-gray-100 dark:bg-gray-800 p-4 rounded-lg">
                        <h3 class="font-semibold mb-3">Gestión del Proceso</h3>

                        <select wire:model="EstadoGeneral"
                            @if(!$editMode) disabled @endif
                            class="w-full p-2 border rounded dark:bg-gray-900">
                            <option>Pendiente</option>
                            <option>En Proceso</option>
                            <option>Decorado</option>
                            <option>Entregado</option>
                        </select>

                        <textarea wire:model="Observacion"
                            @if(!$editMode) disabled @endif
                            class="w-full mt-2 p-2 border rounded dark:bg-gray-900"
                            rows="3"></textarea>
                    </div>

                    {{-- AGENDAMIENTO --}}
                    <div class="bg-gray-100 dark:bg-gray-800 p-4 rounded-lg">
                        <h3 class="font-semibold mb-3">Agendamiento</h3>

                        @if($hasAppointment)

                            <p><b>Fecha:</b> {{ $appointmentDate }}</p>
                            <p><b>Hora:</b> {{ $appointmentTime }}</p>

                            <select wire:model="appointmentStatus"
                                class="w-full mt-2 p-2 border rounded dark:bg-gray-900">
                                <option value="">Seleccione</option>
                                <option value="Asistio">Asistió</option>
                                <option value="No Asistio">No asistió</option>
                            </select>

                            <textarea wire:model="appointmentNotes"
                                class="w-full mt-2 p-2 border rounded dark:bg-gray-900"
                                rows="3"></textarea>

                        @else
                            <p class="text-gray-500">Sin cita registrada</p>
                        @endif
                    </div>

                    {{-- HISTORIAL --}}
                    <div class="bg-gray-100 dark:bg-gray-800 p-4 rounded-lg max-h-[300px] overflow-y-auto">
                        <h3 class="font-semibold mb-3">Historial</h3>

                        @forelse($histories as $history)
                            <div class="border-b py-2">
                                <p class="text-xs text-gray-500">{{ $history->created_at }}</p>
                                <p><b>{{ $history->Estado }}</b></p>
                                <p class="text-sm">{{ $history->Observacion }}</p>
                            </div>
                        @empty
                            <p class="text-gray-500">Sin historial</p>
                        @endforelse
                    </div>

                </div>

                {{-- BOTÓN --}}
                @if($editMode)
                    <div class="mt-6 flex justify-end">
                        <button wire:click="save"
                            class="bg-amber-500 hover:bg-amber-600 text-white px-6 py-2 rounded-lg">
                            Guardar Cambios
                        </button>
                    </div>
                @endif

            </div>
        </div>
    @endif

</x-filament-panels::page>