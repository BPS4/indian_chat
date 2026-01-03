{{-- Footer --}}

<div class="footer bg-white py-4 d-flex flex-lg-column {{ Metronic::printClasses('footer', false) }}" id="kt_footer">
    {{-- Container --}}
    <div
        class="{{ Metronic::printClasses('footer-container', false) }} d-flex flex-column flex-md-row align-items-center justify-content-between">
        {{-- Copyright --}}
        <div class="text-dark order-2 order-md-1">
            <span class="text-muted font-weight-bold mr-2">Copyright &copy {{ date('Y') }} Avana . Designed &
                developed by </span>
            <a href="https://abym.in" target="_blank" class="text-dark-75 text-hover-primary">AbyM Technology.</a>
        </div>


    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
{{-- <script>
    document.addEventListener("DOMContentLoaded", function() {
        console.log("✅ Dashboard charts initialized");
        const labels = @json($monthlyRevenueCharts['labels']);
        const revenueData = @json($monthlyRevenueCharts['data']);
        const bookingData = @json($bookingChart['data']);
        console.log(revenueData);
        console.log(bookingData);
        const ctx1 = document.getElementById('revenueChart');
        const ctx2 = document.getElementById('occupancyChart');

        new Chart(ctx1, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                        label: 'Revenue',
                        data: revenueData,
                        backgroundColor: '#4285F4'
                    },
                    {
                        label: 'Bookings',
                        data: bookingData,
                        backgroundColor: '#C49B66'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        new Chart(ctx2, {
            type: 'pie',
            data: {
                labels: ['Deluxe', 'Suite', 'Luxury', 'Standard', 'Family'],
                datasets: [{
                    data: [699, 480, 761, 304, 218],
                    backgroundColor: ['#795548', '#03A9F4', '#8BC34A', '#FF9800', '#E57373']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    });
</script> --}}
{{-- <script>
    document.addEventListener("DOMContentLoaded", function() {
        const hotelSelect = document.getElementById('hotelFilter');
        const hotelSelectUpcoming = document.getElementById('hotelFilterUpcoming');

        hotelSelect.addEventListener('change', function() {
            let hotelId = this.value;

            // Build URL
            let baseUrl = "{{ route('admin.dashboard') }}";

            if (hotelId) {
                window.location.href = baseUrl + "?hotel_id=" + hotelId;
            } else {
                window.location.href = baseUrl; // reset to all hotels
            }
        });
        hotelSelectUpcoming.addEventListener('change', function() {
            let hotelId = this.value;

            // Build URL
            let baseUrl = "{{ route('admin.dashboard') }}";

            if (hotelId) {
                window.location.href = baseUrl + "?upcoming_hotel_id=" + hotelId;
            } else {
                window.location.href = baseUrl; // reset to all hotels
            }
        });
    });
</script> --}}
