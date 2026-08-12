// =============================================
// Caminhos da História - Uruguaiana
// mapa.js — Mapa Histórico Interativo (Leaflet)
// =============================================

let mapa;
let marcadores = []; // { marker, dados }
let categoriaAtiva = "Todos";

function iniciarMapa() {
    mapa = L.map('mapa').setView([-29.7555, -57.0878], 14);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(mapa);

    carregarLocais();
}

function carregarLocais() {
    fetch('mapa-locais.php')
        .then(resposta => resposta.json())
        .then(dados => {
            dados.forEach(local => criarMarcador(local));
        })
        .catch(erro => {
            console.error('Erro ao carregar os locais:', erro);
        });
}

function criarMarcador(local) {
    const marker = L.marker([local.latitude, local.longitude]).addTo(mapa);

    const popupHtml = `
        <div class="popup-local">
            <span class="popup-categoria">${local.categoria}</span>
            <h3>${local.nome}</h3>
            <p>${local.descricao}</p>
            <button onclick="abrirModal(${local.id})">Ver história</button>
        </div>
    `;

    marker.bindPopup(popupHtml);

    marcadores.push({ marker, dados: local });
}

// ---------- Pesquisa ----------
function pesquisarLocal(termo) {
    termo = termo.trim().toLowerCase();
    if (termo === "") return;

    const encontrado = marcadores.find(item =>
        item.dados.nome.toLowerCase().includes(termo)
    );

    if (encontrado) {
        mapa.flyTo(encontrado.marker.getLatLng(), 17);
        encontrado.marker.openPopup();
    }
}

// ---------- Filtros ----------
function filtrarCategoria(categoria) {
    categoriaAtiva = categoria;

    document.querySelectorAll('.filtro-btn').forEach(btn => {
        btn.classList.toggle('ativo', btn.dataset.categoria === categoria);
    });

    marcadores.forEach(item => {
        const pertence = categoria === "Todos" || item.dados.categoria === categoria;
        if (pertence) {
            if (!mapa.hasLayer(item.marker)) mapa.addLayer(item.marker);
        } else {
            if (mapa.hasLayer(item.marker)) mapa.removeLayer(item.marker);
        }
    });
}

// ---------- Modal de detalhes ----------
function abrirModal(id) {
    const item = marcadores.find(m => m.dados.id == id);
    if (!item) return;

    const local = item.dados;

    document.getElementById('modal-categoria').textContent = local.categoria;
    document.getElementById('modal-nome').textContent = local.nome;
    document.getElementById('modal-historia').textContent = local.historia || 'Sem informações registradas.';
    document.getElementById('modal-importancia').textContent = local.importancia || 'Sem informações registradas.';
    document.getElementById('modal-periodo').textContent = local.periodo_historico || 'Não informado';
    document.getElementById('modal-curiosidades').textContent = local.curiosidades || 'Sem curiosidades registradas.';

    document.getElementById('modal-overlay').classList.add('aberto');
}

function fecharModal() {
    document.getElementById('modal-overlay').classList.remove('aberto');
}

// ---------- Eventos ----------
document.addEventListener('DOMContentLoaded', () => {
    iniciarMapa();

    document.getElementById('pesquisa-local').addEventListener('keyup', (e) => {
        if (e.key === 'Enter') pesquisarLocal(e.target.value);
    });

    document.querySelectorAll('.filtro-btn').forEach(btn => {
        btn.addEventListener('click', () => filtrarCategoria(btn.dataset.categoria));
    });

    document.getElementById('modal-fechar').addEventListener('click', fecharModal);
    document.getElementById('modal-overlay').addEventListener('click', (e) => {
        if (e.target.id === 'modal-overlay') fecharModal();
    });
});
