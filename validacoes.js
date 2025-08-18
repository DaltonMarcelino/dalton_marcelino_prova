function validarFuncionario() {
    let nome = document.getElementById("nome_funcionario").value;
    let telefone = document.getElementById("telefone").value;
    let email = document.getElementById("email").value;

    if (nome.length < 3) {
        alert("O nome do funcionário deve ter pelo menos 3 caracteres.");
        return false;
    }

    let regexTelefone = /^[0-9]{10,11}$/;
    if (!regexTelefone.test(telefone)) {
        alert("Digite um telefone válido (10 ou 11 dígitos).");
        return false;
    }

    let regexEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!regexEmail.test(email)) {
        alert("Digite um e-mail válido.");
        return false;
    }

    return true;
}

document.addEventListener('DOMContentLoaded', function() {
    const campoNome = document.getElementById('nome');
    
    campoNome.addEventListener('input', function() {
        // Remove qualquer caractere que não seja letra, espaço ou acento
        this.value = this.value.replace(/[^A-Za-zÀ-ú\s]/g, '');
    });
    
    campoNome.addEventListener('blur', function() {
        // Verifica se após a edição ainda há algum caractere inválido
        if (/[^A-Za-zÀ-ú\s]/.test(this.value)) {
            alert('Por favor, digite apenas letras no campo de nome.');
            this.value = this.value.replace(/[^A-Za-zÀ-ú\s]/g, '');
            this.focus();
        }
    });
});