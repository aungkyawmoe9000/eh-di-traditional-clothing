<div class="p-6 bg-white dark:bg-neutral-900 rounded-xl shadow-sm border border-neutral-200 dark:border-neutral-800">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">Daily Register & Reports</h1>
            <p class="text-sm text-neutral-500 dark:text-neutral-400">Monitor cash register status, record closing balances, and view transaction history.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $status === 'open' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300' : 'bg-neutral-100 text-neutral-800 dark:bg-neutral-800 dark:text-neutral-300' }}">
                <span class="w-2 h-2 mr-1.5 rounded-full {{ $status === 'open' ? 'bg-emerald-500' : 'bg-neutral-500' }}"></span>
                Status: {{ ucfirst($status) }}
            </span>
            <span class="inline-flex items-center px-3 py-1 bg-neutral-100 dark:bg-neutral-800 text-neutral-700 dark:text-neutral-300 rounded-full text-xs font-medium">
                Date: {{ \Carbon\Carbon::parse($date)->format('M d, Y') }}
            </span>
        </div>
    </div>

    <!-- Session Flash Messages -->
    @if (session()->has('success'))
        <div class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800/50 text-emerald-800 dark:text-emerald-300 rounded-lg flex items-center justify-between" x-data="{ show: true }" x-show="show" x-transition>
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2 text-emerald-600 dark:text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <span class="text-sm font-medium">{{ session('success') }}</span>
            </div>
            <button @click="show = false" class="text-emerald-600 hover:text-emerald-800 dark:text-emerald-400 dark:hover:text-emerald-200 cursor-pointer">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-6 p-4 bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-800/50 text-rose-800 dark:text-rose-300 rounded-lg flex items-center justify-between" x-data="{ show: true }" x-show="show" x-transition>
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2 text-rose-600 dark:text-rose-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
                <span class="text-sm font-medium">{{ session('error') }}</span>
            </div>
            <button @click="show = false" class="text-rose-600 hover:text-rose-800 dark:text-rose-400 dark:hover:text-rose-200 cursor-pointer">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>
    @endif

    <!-- Top Section (Overview Cards) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <!-- Opening Balance -->
        <div class="p-5 bg-neutral-50 dark:bg-neutral-800/40 rounded-xl border border-neutral-200 dark:border-neutral-800 flex items-center gap-4">
            <div class="p-3 bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-300 rounded-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-2m-4-1v8m0 0l3-3m-3 3L9 8m-5 5h2.586a1 1 0 01.707.293l2.414 2.414a1 1 0 00.707.293h3.172a1 1 0 00.707-.293l2.414-2.414a1 1 0 01.707-.293H20" />
                </svg>
            </div>
            <div>
                <span class="block text-xs font-medium text-neutral-500 dark:text-neutral-400">Opening Balance</span>
                <span class="text-xl font-bold text-neutral-900 dark:text-neutral-100">${{ number_format($opening_balance, 2) }}</span>
            </div>
        </div>

        <!-- Today's Total Sales -->
        <div class="p-5 bg-neutral-50 dark:bg-neutral-800/40 rounded-xl border border-neutral-200 dark:border-neutral-800 flex items-center gap-4">
            <div class="p-3 bg-emerald-50 dark:bg-emerald-950/30 text-emerald-600 dark:text-emerald-400 rounded-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <span class="block text-xs font-medium text-neutral-500 dark:text-neutral-400">Today's Cash Sales</span>
                <span class="text-xl font-bold text-neutral-900 dark:text-neutral-100">${{ number_format($total_cash_sales, 2) }}</span>
            </div>
        </div>

        <!-- Gross Profit -->
        <div class="p-5 bg-neutral-50 dark:bg-neutral-800/40 rounded-xl border border-neutral-200 dark:border-neutral-800 flex items-center gap-4">
            <div class="p-3 bg-blue-50 dark:bg-blue-950/30 text-blue-600 dark:text-blue-400 rounded-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <span class="block text-xs font-medium text-neutral-500 dark:text-neutral-400">Gross Profit</span>
                <span class="text-xl font-bold text-neutral-900 dark:text-neutral-100">${{ number_format($total_cash_sales - $total_expenses, 2) }}</span>
                <span class="block text-[10px] text-neutral-400 dark:text-neutral-500 truncate">Expenses: ${{ number_format($total_expenses, 2) }}</span>
            </div>
        </div>

        <!-- Expected Closing Balance -->
        <div class="p-5 bg-neutral-50 dark:bg-neutral-800/40 rounded-xl border border-neutral-200 dark:border-neutral-800 flex items-center gap-4">
            <div class="p-3 bg-indigo-50 dark:bg-indigo-950/30 text-indigo-600 dark:text-indigo-400 rounded-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
            </div>
            <div>
                <span class="block text-xs font-medium text-neutral-500 dark:text-neutral-400">Expected Closing</span>
                <span class="text-xl font-bold text-neutral-900 dark:text-neutral-100">${{ number_format($expected_closing_balance, 2) }}</span>
            </div>
        </div>
    </div>

    <!-- Middle Section (Close Register Action) -->
    @if($status === 'open')
        <div class="mb-8 p-6 bg-neutral-50 dark:bg-neutral-800/30 rounded-xl border border-neutral-200 dark:border-neutral-800">
            <h2 class="text-lg font-bold text-neutral-900 dark:text-neutral-100 mb-1">Close Register</h2>
            <p class="text-sm text-neutral-500 dark:text-neutral-400 mb-6">Verify cash sales & expenses, count physical drawer cash, and enter actual closing balance.</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                <div class="space-y-4">
                    <div>
                        <flux:input 
                            type="number" 
                            step="0.01" 
                            wire:model.live="actual_closing_balance" 
                            label="Actual Closing Balance ($)" 
                            placeholder="0.00" 
                            class="w-full"
                        />
                        @error('actual_closing_balance') 
                            <span class="text-xs text-rose-600 mt-1 block font-medium">{{ $message }}</span> 
                        @enderror
                    </div>

                    <div class="pt-2">
                        <flux:button 
                            wire:click="closeRegister" 
                            wire:confirm="Are you sure you want to close today's register? This action is irreversible and will lock today's reports."
                            variant="primary" 
                            class="w-full sm:w-auto cursor-pointer font-semibold"
                        >
                            Close Register
                        </flux:button>
                    </div>
                </div>

                <div>
                    <!-- Discrepancy Display Card -->
                    @if ($actual_closing_balance !== null && $actual_closing_balance !== '')
                        <div class="p-5 rounded-xl flex items-center justify-between border {{ $discrepancy < 0 ? 'bg-rose-50/50 dark:bg-rose-950/10 border-rose-200 dark:border-rose-800/40 text-rose-800 dark:text-rose-300' : 'bg-emerald-50/50 dark:bg-emerald-950/10 border-emerald-200 dark:border-emerald-800/40 text-emerald-800 dark:text-emerald-300' }}">
                            <div class="space-y-1">
                                <span class="block text-xs font-semibold uppercase tracking-wider text-neutral-500 dark:text-neutral-400">Drawer Discrepancy</span>
                                <span class="text-2xl font-black block">
                                    @if($discrepancy < 0)
                                        -${{ number_format(abs($discrepancy), 2) }}
                                    @elseif($discrepancy > 0)
                                        +${{ number_format($discrepancy, 2) }}
                                    @else
                                        $0.00
                                    @endif
                                </span>
                                <span class="block text-xs font-medium">
                                    @if($discrepancy < 0)
                                        <span class="font-bold">Shortage</span> — drawer is under expected amount.
                                    @elseif($discrepancy > 0)
                                        <span class="font-bold">Overage</span> — drawer has surplus funds.
                                    @else
                                        <span class="font-bold">Balanced</span> — drawer matches expectations exactly.
                                    @endif
                                </span>
                            </div>
                        </div>
                    @else
                        <div class="p-5 bg-neutral-100 dark:bg-neutral-800/30 rounded-xl border border-neutral-200/60 dark:border-neutral-800 text-neutral-500 dark:text-neutral-400 flex flex-col justify-center items-center text-center h-full min-h-[120px]">
                            <span class="text-xs font-semibold uppercase tracking-wider text-neutral-400 dark:text-neutral-500">Awaiting Balance Input</span>
                            <span class="text-xs mt-1 max-w-xs text-center">Count and enter the actual cash to calculate discrepancies.</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Bottom Section (History Table) -->
    <div class="mt-8">
        <div class="mb-4">
            <h2 class="text-lg font-bold text-neutral-900 dark:text-neutral-100">Closed Registers History</h2>
            <p class="text-sm text-neutral-500 dark:text-neutral-400">Historical record of all closed register days and discrepancies.</p>
        </div>

        <div class="overflow-x-auto border border-neutral-200 dark:border-neutral-800 rounded-lg bg-white dark:bg-neutral-900">
            <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-800 text-left text-sm">
                <thead class="bg-neutral-50 dark:bg-neutral-800/50 text-neutral-700 dark:text-neutral-300 font-semibold uppercase tracking-wider text-xs">
                    <tr>
                        <th scope="col" class="px-6 py-4">Date</th>
                        <th scope="col" class="px-6 py-4">Opening</th>
                        <th scope="col" class="px-6 py-4">Cash Sales</th>
                        <th scope="col" class="px-6 py-4">Expenses</th>
                        <th scope="col" class="px-6 py-4">Expected Closing</th>
                        <th scope="col" class="px-6 py-4">Actual Closing</th>
                        <th scope="col" class="px-6 py-4">Discrepancy</th>
                        <th scope="col" class="px-6 py-4">Closed By</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-800 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100">
                    @forelse ($history as $report)
                        <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/30 transition-colors">
                            <td class="px-6 py-4 font-semibold whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($report->date)->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-neutral-600 dark:text-neutral-300">
                                ${{ number_format($report->opening_balance, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-emerald-600 dark:text-emerald-400 font-medium">
                                +${{ number_format($report->total_cash_sales, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-rose-600 dark:text-rose-400 font-medium">
                                -${{ number_format($report->total_expenses, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap font-medium text-neutral-600 dark:text-neutral-300">
                                ${{ number_format($report->expected_closing_balance, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap font-bold text-neutral-900 dark:text-neutral-100">
                                ${{ number_format($report->actual_closing_balance, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap font-bold">
                                @if($report->discrepancy < 0)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-100 text-rose-800 dark:bg-rose-950/40 dark:text-rose-300">
                                        -${{ number_format(abs($report->discrepancy), 2) }} Short
                                    </span>
                                @elseif($report->discrepancy > 0)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300">
                                        +${{ number_format($report->discrepancy, 2) }} Over
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-neutral-100 text-neutral-800 dark:bg-neutral-800 dark:text-neutral-300">
                                        Balanced
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-neutral-600 dark:text-neutral-400 text-xs">
                                {{ $report->closedByUser->name ?? 'System' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-neutral-500 dark:text-neutral-400">
                                No closed register history found. Once closed, records appear here.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $history->links() }}
        </div>
    </div>

</div>

