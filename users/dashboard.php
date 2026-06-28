<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include "../config/koneksi.php";

if(!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin'){
    header("Location: ../auth/login.php"); exit;
}

// 1) Distribusi role pengguna
$q5 = mysqli_query($conn, "SELECT role, COUNT(*) AS cnt FROM users GROUP BY role");
$role_labels = [];
$role_data = [];
while($r = mysqli_fetch_assoc($q5)){
    $role_labels[] = ucfirst($r['role']);
    $role_data[] = (int)$r['cnt'];
}

include "../components/header.php";
?>

<span class="h1-tema">Dashboard Admin — Statistik</span>


<div style="background:#fff; padding:18px; border-radius:6px; max-width:660px; margin:auto;">
    <h3 style="margin-top:0;">Admin / Perusahaan / Pelamar</h3>
    <div style="position:relative; width:100%; max-width:560px; height:360px; margin:0 auto;">
        <canvas id="roleChart"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
<script>
const roleLabels = <?= json_encode($role_labels); ?>;
const roleData = <?= json_encode($role_data); ?>;

// Role doughnut
new Chart(document.getElementById('roleChart').getContext('2d'), {
    type: 'doughnut',
    data: { labels: roleLabels, datasets:[{ data: roleData, backgroundColor:[ '#6f42c1', '#20c997', '#fd7e14', '#007bff' ] }] },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'bottom' },
            datalabels: {
                color: '#fff',
                formatter: (value, ctx) => {
                    const data = ctx.chart.data.datasets[0].data;
                    const sum = data.reduce((total, v) => total + v, 0);
                    if (!sum) return '';
                    const percentage = (value / sum * 100).toFixed(0);
                    return percentage + '%';
                },
                font: { weight: 'bold', size: 14 },
                anchor: 'center',
                align: 'center'
            }
        },
        layout: { padding: 10 }
    },
    plugins: [ChartDataLabels]
});
</script>

<?php include "../components/footer.php"; ?>
