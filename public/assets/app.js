async function fetchStatus(reportId){
    const res = await fetch('/public/api/get_status.php?report_id=' + encodeURIComponent(reportId));
    if(!res.ok) throw new Error('Network error');
    return res.json();
}
let pollingHandle = null;
function startPolling(reportId){
    const infoEl = document.getElementById('report-info') || document.getElementById('resultArea');
    const statusEl = document.getElementById('current-status');
    if(pollingHandle) clearInterval(pollingHandle);
    async function tick(){
        try{
            const data = await fetchStatus(reportId);
            if(infoEl && typeof window.showTrackResult === 'function'){
                window.showTrackResult(data);
            } else if(infoEl){
                if(data.success){
                    const d = data.data;
                    infoEl.innerHTML = '<div class="mt-3"><h5>'+ (d.kategori||'-') +'</h5><p>'+d.isi+'</p></div>';
                    if(statusEl) { statusEl.textContent = d.status; statusEl.className = 'badge bg-status-'+d.status.replace(/\s+/g,''); }
                } else {
                    infoEl.innerHTML = '<div class="alert alert-warning">Laporan tidak ditemukan.</div>';
                }
            }
        } catch(e){ console.error(e); }
    }
    tick();
    pollingHandle = setInterval(tick, 3000);
}
function stopPolling(){ if(pollingHandle) clearInterval(pollingHandle); }
