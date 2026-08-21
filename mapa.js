// =============================================
// Caminhos da História - Uruguaiana
// mapa.js — Mapa Histórico Interativo (Leaflet)
// =============================================

let mapa;
let marcadores = []; // { marker, dados }
let categoriaAtiva = "Todos";

// Cor do pin por categoria (mesmo espírito visual do mapa ilustrado de referência)
const coresCategoria = {
    'Patrimônio Histórico':    '#c9a227',
    'Patrimônio Religioso':    '#e0a83e',
    'Patrimônios Imateriais':  '#8e44ad',
    'Cultura':                 '#2c2c2c',
    'Tradição Gaúcha':         '#C0392B',
    'Turismo':                 '#27ae60',
    'Integração Regional':     '#3498db'
};

function corDaCategoria(categoria) {
    return coresCategoria[categoria] || '#C0392B';
}

// Cria um ícone de pin em SVG na cor da categoria
function criarIconePin(cor) {
    const svg = `
        <svg width="34" height="44" viewBox="0 0 40 52" xmlns="http://www.w3.org/2000/svg">
            <path d="M20 2C10.6 2 3 9.6 3 19c0 13 17 31 17 31s17-18 17-31C37 9.6 29.4 2 20 2z"
                  fill="${cor}" stroke="rgba(0,0,0,0.25)" stroke-width="1"/>
            <circle cx="20" cy="19" r="7.5" fill="#ffffff"/>
        </svg>
    `;

    return L.divIcon({
        html: svg,
        className: 'pin-icone',
        iconSize: [34, 44],
        iconAnchor: [17, 44],
        popupAnchor: [0, -40],
        tooltipAnchor: [0, 4]
    });
}

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
    const cor = corDaCategoria(local.categoria);
    const icone = criarIconePin(cor);

    const marker = L.marker([local.latitude, local.longitude], { icon: icone }).addTo(mapa);

    // Nome do local sempre visível embaixo do marcador, no estilo "etiqueta"
    marker.bindTooltip(local.nome, {
        permanent: true,
        direction: 'bottom',
        offset: [0, 6],
        className: 'marcador-label'
    });

    const imagemHtml = local.imagem
        ? `<img src="${local.imagem}" class="popup-imagem" alt="${local.nome}">`
        : '';

    const popupHtml = `
        <div class="popup-local">
            ${imagemHtml}
            <span class="popup-categoria" style="color:${cor};">${local.categoria}</span>
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
let localAtualId = null;

function abrirModal(id) {
    const item = marcadores.find(m => m.dados.id == id);
    if (!item) return;

    const local = item.dados;
    localAtualId = local.id;

    document.getElementById('modal-categoria').textContent = local.categoria;
    document.getElementById('modal-nome').textContent = local.nome;
    document.getElementById('modal-historia').textContent = local.historia || 'Sem informações registradas.';
    document.getElementById('modal-importancia').textContent = local.importancia || 'Sem informações registradas.';
    document.getElementById('modal-periodo').textContent = local.periodo_historico || 'Não informado';
    document.getElementById('modal-curiosidades').textContent = local.curiosidades || 'Sem curiosidades registradas.';

    // Imagem principal no modal
    const modalImagem = document.getElementById('modal-imagem');
    if (local.imagem) {
        modalImagem.src = local.imagem;
        modalImagem.style.display = 'block';
    } else {
        modalImagem.style.display = 'none';
    }

    montarLinhaDoTempo(local.linha_do_tempo || []);
    montarCausos(local.causos || []);

    document.getElementById('modal-enviar-causo').href = 'enviar-causo.php?local_id=' + local.id;

    document.getElementById('modal-overlay').classList.add('aberto');
}

function fecharModal() {
    document.getElementById('modal-overlay').classList.remove('aberto');
}

// ---------- Linha do tempo (imagens por período) ----------
function montarLinhaDoTempo(imagens) {
    const container = document.getElementById('modal-timeline');
    container.innerHTML = '';

    if (!imagens.length) {
        document.getElementById('modal-timeline-wrap').style.display = 'none';
        return;
    }

    document.getElementById('modal-timeline-wrap').style.display = 'block';

    imagens.forEach((img, index) => {
        const tab = document.createElement('button');
        tab.className = 'timeline-tab' + (index === 0 ? ' ativo' : '');
        tab.textContent = img.periodo;
        tab.onclick = () => selecionarPeriodo(imagens, index);
        container.appendChild(tab);
    });

    selecionarPeriodo(imagens, 0);
}

function selecionarPeriodo(imagens, index) {
    const img = imagens[index];

    document.querySelectorAll('.timeline-tab').forEach((tab, i) => {
        tab.classList.toggle('ativo', i === index);
    });

    document.getElementById('modal-timeline-foto').src = img.imagem;
    document.getElementById('modal-timeline-legenda').textContent = img.legenda || img.periodo;
}

// ---------- Causos aprovados ----------
function montarCausos(causos) {
    const lista = document.getElementById('modal-causos-lista');
    lista.innerHTML = '';

    if (!causos.length) {
        lista.innerHTML = '<p class="sem-causos">Nenhum causo enviado para este local ainda.</p>';
        return;
    }

    causos.forEach(causo => {
        const item = document.createElement('div');
        item.className = 'causo-modal-item';
        item.innerHTML = `
            <strong>${causo.titulo}</strong>
            <p>${causo.texto}</p>
            <span class="causo-modal-autor">por ${causo.autor}</span>
        `;
        lista.appendChild(item);
    });
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