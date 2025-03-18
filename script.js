let currentDate = new Date();
let events = [];

function renderCalendar() {
    const monthYear = document.getElementById('currentMonth');
    const daysContainer = document.getElementById('days');
    daysContainer.innerHTML = '';

    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();
    monthYear.textContent = currentDate.toLocaleString('pt-BR', { month: 'long', year: 'numeric' }).replace(/^\w/, c => c.toUpperCase());

    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();

    for (let i = 0; i < firstDay; i++) {
        const emptyDay = document.createElement('div');
        emptyDay.className = 'day empty';
        daysContainer.appendChild(emptyDay);
    }

    for (let day = 1; day <= daysInMonth; day++) {
        const dayElement = document.createElement('div');
        dayElement.className = 'day';
        dayElement.innerHTML = `<span>${day}</span>`;
        const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        
        dayElement.addEventListener('click', (e) => {
            if (!e.target.classList.contains('event')) {
                openModal(dateStr);
            }
        });
        
        const dayEvents = events.filter(event => event.data === dateStr);
        if (dayEvents.length > 0) {
            const eventsText = document.createElement('div');
            eventsText.className = 'event';
            eventsText.textContent = 'Eventos Cadastrados';
            eventsText.addEventListener('click', () => showEventList(dateStr, dayEvents));
            dayElement.appendChild(eventsText);
        }

        daysContainer.appendChild(dayElement);
    }
}

function showEventList(date, dayEvents) {
    let eventList = '<h2>Eventos do Dia</h2>';
    dayEvents.forEach((event, index) => {
        eventList += `<div class="event-detail">${event.titulo} - ${event.horario.slice(0, 5)}</div>`;
        if (index < dayEvents.length - 1) {
            eventList += '<hr>';
        }
    });

    Swal.fire({
        title: '',
        html: eventList,
        showCloseButton: true,
        showConfirmButton: true,
        confirmButtonText: 'OK',
        confirmButtonColor: '#4CAF50',
        focusConfirm: false,
        customClass: {
            popup: 'event-list-modal',
            title: 'event-list-title',
            content: 'event-list-content',
            confirmButton: 'swal2-confirm-ok'
        }
    });
}

function openModal(date) {
    const modal = document.getElementById('eventModal');
    const dataInput = document.getElementById('data');
    dataInput.value = date;
    modal.style.display = 'block';

    const close = document.getElementsByClassName('close')[0];
    close.onclick = () => modal.style.display = 'none';

    window.onclick = (event) => {
        if (event.target == modal) modal.style.display = 'none';
    };
}

document.getElementById('prevMonth').addEventListener('click', () => {
    currentDate.setMonth(currentDate.getMonth() - 1);
    renderCalendar();
    fetchEvents();
});

document.getElementById('nextMonth').addEventListener('click', () => {
    currentDate.setMonth(currentDate.getMonth() + 1);
    renderCalendar();
    fetchEvents();
});

document.getElementById('eventForm').addEventListener('submit', (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData);

    fetch('salvar-evento.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.error) {
            Swal.fire('Erro', result.error, 'error');
        } else {
            Swal.fire('Sucesso', result.message, 'success');
            document.getElementById('eventModal').style.display = 'none';
            e.target.reset();
            fetchEvents();
        }
    })
    .catch(error => {
        Swal.fire('Erro', 'Erro ao salvar evento: ' + error.message, 'error');
    });
});

function fetchEvents() {
    fetch('./listar-eventos.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Erro na requisição: Status ' + response.status);
            }
            return response.text(); // Captura a resposta bruta como texto
        })
        .then(text => {
            console.log('Resposta bruta do listar-eventos.php:', text); // Log da resposta bruta
            // Tentar remover qualquer conteúdo antes do JSON (ex.: comentários)
            const jsonStart = text.indexOf('[');
            const cleanText = jsonStart >= 0 ? text.substring(jsonStart) : text;
            try {
                const data = JSON.parse(cleanText);
                if (Array.isArray(data)) {
                    events = data;
                    renderCalendar();
                } else {
                    throw new Error('Dados não são um array');
                }
            } catch (error) {
                console.error('Erro ao parsear JSON:', error, 'Texto limpo:', cleanText);
                Swal.fire('Erro', 'Erro ao carregar eventos: Formato inválido ou servidor retornou HTML', 'error');
            }
        })
        .catch(error => {
            console.error('Erro ao carregar eventos:', error);
            Swal.fire('Erro', 'Erro ao carregar eventos: ' + error.message, 'error');
        });
}

// Inicializar o calendário ao carregar a página
window.onload = () => {
    renderCalendar();
    fetchEvents();
};