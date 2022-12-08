<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Reports') }}
        </h2>
    </x-slot>


        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <h2 class="font-semibold text-lg text-gray-800 leading-tight mb-5">
                    Grafik Penjualan Tiap Bulan
                </h2>
                <div class="bg-white overflow-hiddenavn shadow sm:rounded-lg mb-18">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="header-title" align="center">Grafik Penjualan</h4>
                                <canvas id="mataChart" class="chartjs" width="300px" height="100px"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.bundle.min.js"></script>
        <script type="text/javascript">
            var ctx = document.getElementById("mataChart").getContext('2d');
            var myChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode($label); ?>,
                    datasets: [{
                        label: 'Total Transaksi Sukses',
                        backgroundColor: '#ADD8E6',
                        borderColor: '#93C3D2',
                        data: <?php echo json_encode($jumlah_user); ?>
                    }],
                    options: {
                        animation: {
                            onProgress: function(animation) {
                                progress.value = animation.animationObject.currentStep / animation.animationObject
                                    .numSteps;
                            }
                        }
                    }
                },
            });
        </script>
</x-app-layout>
