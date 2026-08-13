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

    // Nome do local sempre visível embaixo do marcador
    marker.bindTooltip(local.nome, {
        permanent: true,
        direction: 'bottom',
        offset: [0, 8],
        className: 'marcador-label'
    });

    const imagemHtml = local.imagem
        ? `<img src="${local.imagem}" class="popup-imagem" alt="${local.nome}">`
        : '';

    const popupHtml = `
        <div class="popup-local">
            ${imagemHtml}
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
    const foto = document.getElementById('modal-timeline-foto');
    const legenda = document.getElementById('modal-timeline-legenda');

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