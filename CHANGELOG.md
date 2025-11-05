# Changelog - Shopping Cart Application

## [1.1.0] - 2025-11-05

### 🐛 Bug Fixes

#### Frontend (React)

**Problema 1: Layout do carrinho quebrado**
- ✅ **Corrigido**: Alterado layout de `grid` para `flexbox` no componente de item do carrinho
- ✅ **Melhorias**: 
  - Imagens maiores (80px × 80px)
  - Espaçamento melhorado entre elementos
  - Layout responsivo que se adapta melhor em diferentes tamanhos de tela
  - Controles de quantidade com fundo e melhor visual
  - Botão "Remove" com efeitos hover aprimorados

**Problema 2: Botão "Calculando..." não muda após cálculo**
- ✅ **Corrigido**: URL da API atualizada de `http://localhost:8000` para `http://localhost:8003`
- ✅ **Melhorias**:
  - Reset automático do total quando o carrinho é modificado
  - Feedback visual claro quando itens são adicionados/removidos
  - Estado de loading funcional

#### Backend (Laravel Octane)

**Infraestrutura**
- ✅ **Swoole instalado**: Extensão Swoole adicionada ao Dockerfile
- ✅ **Node.js atualizado**: Frontend usando Node.js 20 (requerido pelo Vite)
- ✅ **Porta 8003**: Backend configurado para rodar na porta 8003

### 📝 Arquivos Modificados

1. `frontend/src/App.jsx`
   - Linha 4: API_URL atualizada para porta 8003
   - Linhas 44, 49, 60: Reset de cartTotal quando carrinho muda

2. `frontend/src/App.css`
   - Linhas 153-171: Layout do cart-item corrigido
   - Linhas 173-187: Item details melhorado
   - Linhas 189-234: Controles de quantidade e total redesenhados
   - Linhas 236-253: Botão remove estilizado

3. `frontend/Dockerfile`
   - Linha 1: Node.js atualizado de 18-alpine para 20-alpine

4. `backend/Dockerfile`
   - Linhas 13-15: Dependências para Swoole adicionadas
   - Linhas 23-25: Instalação do Swoole via PECL

5. `docker-compose.yml`
   - Linha 10: Porta backend alterada de 8000 para 8003
   - Linha 20: Command do Octane usando porta 8003
   - Linha 33: VITE_API_URL atualizada para porta 8003

6. `README.md`
   - Linha 49: URL da API atualizada na documentação

### ✅ Testes

Todos os testes continuam passando:
```
✅ 23 tests passed (108 assertions)
⏱️ Duration: 1.20s
```

### 🌐 URLs Atualizadas

- **Frontend**: http://localhost:3000
- **Backend API**: http://localhost:8003
- **Produtos**: http://localhost:8003/api/products
- **Cálculo**: http://localhost:8003/api/cart/calculate

### 🎨 Melhorias Visuais

#### Antes vs Depois

**Layout do Item do Carrinho:**
- ❌ Antes: Grid rígido com 5 colunas que quebrava em telas menores
- ✅ Depois: Flexbox responsivo que se adapta ao tamanho da tela

**Controles de Quantidade:**
- ❌ Antes: Botões simples sem feedback visual
- ✅ Depois: Botões com fundo, hover animado e melhor espaçamento

**Estado de Loading:**
- ❌ Antes: Botão "Calculando..." não mudava (erro de API)
- ✅ Depois: Loading funcional com reset automático do total

### 🚀 Como Testar

1. Acesse http://localhost:3000
2. Adicione produtos ao carrinho
3. Observe o layout melhorado dos itens
4. Ajuste quantidades usando os botões +/-
5. Selecione um método de pagamento
6. Clique em "Calculate Total"
7. Observe que o botão muda para "Calculando..." e depois volta
8. Veja o resumo do carrinho com os valores calculados

### 📊 Status

✅ **Layout corrigido**  
✅ **API funcionando**  
✅ **Loading funcionando**  
✅ **Testes passando**  
✅ **Documentação atualizada**

### 🎯 Próximas Versões

Possíveis melhorias futuras:
- [ ] Salvar carrinho no localStorage
- [ ] Adicionar animações de transição
- [ ] Modo escuro
- [ ] Responsividade aprimorada para mobile
- [ ] Adicionar testes E2E

---

## [1.0.0] - 2025-11-05

### 🎉 Lançamento Inicial

- ✅ Docker Compose configurado
- ✅ Backend Laravel Octane
- ✅ Frontend React + Vite
- ✅ 5 produtos hardcoded
- ✅ 3 métodos de pagamento
- ✅ Fórmula de juros compostos
- ✅ 23 testes (108 assertions)
- ✅ Documentação completa

