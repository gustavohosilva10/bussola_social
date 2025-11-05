import { useState, useEffect } from 'react'
import './App.css'

const API_URL = import.meta.env.VITE_API_URL || 'http://localhost:8003'

function App() {
  const [products, setProducts] = useState([])
  const [cart, setCart] = useState([])
  const [paymentMethod, setPaymentMethod] = useState('PIX')
  const [installments, setInstallments] = useState(1)
  const [cartTotal, setCartTotal] = useState(null)
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState(null)

  useEffect(() => {
    fetchProducts()
  }, [])

  const fetchProducts = async () => {
    try {
      const response = await fetch(`${API_URL}/api/products`)
      const data = await response.json()
      if (data.success) {
        setProducts(data.data)
      }
    } catch (err) {
      setError('Falha ao carregar produtos')
    }
  }

  const addToCart = (product) => {
    const existingItem = cart.find(item => item.product_id === product.id)
    
    if (existingItem) {
      setCart(cart.map(item =>
        item.product_id === product.id
          ? { ...item, quantity: item.quantity + 1 }
          : item
      ))
    } else {
      setCart([...cart, { product_id: product.id, quantity: 1 }])
    }
    setCartTotal(null)
  }

  const removeFromCart = (productId) => {
    setCart(cart.filter(item => item.product_id !== productId))
    setCartTotal(null) 
  }

  const updateQuantity = (productId, newQuantity) => {
    if (newQuantity < 1) return
    
    setCart(cart.map(item =>
      item.product_id === productId
        ? { ...item, quantity: newQuantity }
        : item
    ))
    setCartTotal(null) 
  }

  const calculateCart = async () => {
    if (cart.length === 0) {
      setError('Carrinho está vazio')
      return
    }

    setLoading(true)
    setError(null)

    try {

      const response = await fetch(`${API_URL}/api/cart/calculate`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          items: cart,
          payment_method: paymentMethod,
          installments: paymentMethod === 'CREDIT_CARD_INSTALLMENTS' ? installments : 1,
        }),
      })

      const data = await response.json()
      
      if (data.success) {
        setCartTotal(data.data)
      } else {
        setError(data.message || 'Falha ao calcular o carrinho')
      }
    } catch (err) {
      setError(`Falha ao calcular o carrinho: ${err.message}`)
    } finally {
      setTimeout(() => {
        setLoading(false)
      }, 0)
    }
  }

  const getProductById = (productId) => {
    return products.find(p => p.id === productId)
  }

  const getPaymentMethodLabel = (method) => {
    const labels = {
      'PIX': 'PIX (10% de desconto)',
      'CREDIT_CARD_FULL_PAYMENT': 'Cartão de Crédito - Pagamento Integral (10% de desconto)',
      'CREDIT_CARD_INSTALLMENTS': 'Cartão de Crédito - Parcelamento (1% de juros por parcela)',
    }
    return labels[method] || method
  }


  return (
    <div className="app">
      <header>
        <h1>Carrinho de Compras</h1>
      </header>

      <main>
        <section className="products-section">
          <h2>Produtos</h2>
          <div className="products-grid">
            {products.map(product => (
              <div key={product.id} className="product-card">
                <img src={product.image_url} alt={product.name} />
                <h3>{product.name}</h3>
                <p className="description">{product.description}</p>
                <p className="price">R$ {product.price.toFixed(2)}</p>
                <button onClick={() => addToCart(product)}>
                  Adicionar ao Carrinho
                </button>
              </div>
            ))}
          </div>
        </section>

        <section className="cart-section">
          <h2>Carrinho de Compras ({cart.length} {cart.length === 1 ? 'item' : 'itens'})</h2>
          
          {cart.length === 0 ? (
            <p className="empty-cart">Seu carrinho está vazio</p>
          ) : (
            <>
              <div className="cart-items">
                {cart.map(item => {
                  const product = getProductById(item.product_id)
                  if (!product) return null
                  
                  return (
                    <div key={item.product_id} className="cart-item">
                      <img src={product.image_url} alt={product.name} />
                      <div className="item-details">
                        <h4>{product.name}</h4>
                        <p>R$ {product.price.toFixed(2)} cada</p>
                      </div>
                      <div className="quantity-controls">
                        <button onClick={() => updateQuantity(item.product_id, item.quantity - 1)}>-</button>
                        <span>{item.quantity}</span>
                        <button onClick={() => updateQuantity(item.product_id, item.quantity + 1)}>+</button>
                      </div>
                      <p className="item-total">R$ {(product.price * item.quantity).toFixed(2)}</p>
                      <button 
                        className="remove-btn"
                        onClick={() => removeFromCart(item.product_id)}
                      >
                        Remover
                      </button>
                    </div>
                  )
                })}
              </div>

              <div className="payment-section">
                <h3>Método de Pagamento</h3>
                <select 
                  value={paymentMethod} 
                  onChange={(e) => setPaymentMethod(e.target.value)}
                >
                  <option value="PIX">PIX (10% de desconto)</option>
                  <option value="CREDIT_CARD_FULL_PAYMENT">Cartão de Crédito - Pagamento Integral (10% de desconto)</option>
                  <option value="CREDIT_CARD_INSTALLMENTS">Cartão de Crédito - Parcelamento</option>
                </select>

                {paymentMethod === 'CREDIT_CARD_INSTALLMENTS' && (
                  <div className="installments">
                    <label>
                      Parcelas (2-12):
                      <input 
                        type="number" 
                        min="2" 
                        max="12" 
                        value={installments}
                        onChange={(e) => setInstallments(parseInt(e.target.value))}
                      />
                    </label>
                    <small>Taxa de juros: 1% por parcela (juros compostos)</small>
                  </div>
                )}

                <button 
                  key={`calc-btn-${loading}`}
                  className="calculate-btn"
                  onClick={calculateCart}
                  disabled={loading}
                >
                  {loading ? 'Calculando...' : 'Calcular Total'}
                </button>
              </div>

              {error && (
                <div className="error-message">
                  {error}
                </div>
              )}

              {cartTotal && (
                <div className="cart-total">
                  <h3>Resumo do Carrinho</h3>
                  <div className="total-row">
                    <span>Subtotal:</span>
                    <span>R$ {cartTotal.subtotal.toFixed(2)}</span>
                  </div>
                  {cartTotal.discount > 0 && (
                    <div className="total-row discount">
                      <span>Desconto:</span>
                      <span>- R$ {cartTotal.discount.toFixed(2)}</span>
                    </div>
                  )}
                  {cartTotal.interest > 0 && (
                    <div className="total-row interest">
                      <span>Juros:</span>
                      <span>+ R$ {cartTotal.interest.toFixed(2)}</span>
                    </div>
                  )}
                  <div className="total-row final">
                    <span>Total Final:</span>
                    <span>R$ {cartTotal.final_value.toFixed(2)}</span>
                  </div>
                  {cartTotal.installment_value && (
                    <div className="total-row">
                      <span>{cartTotal.installments}x de:</span>
                      <span>R$ {cartTotal.installment_value.toFixed(2)}</span>
                    </div>
                  )}
                  <p className="payment-info">
                    Pagamento: {getPaymentMethodLabel(cartTotal.payment_method)}
                  </p>
                </div>
              )}
            </>
          )}
        </section>
      </main>
    </div>
  )
}

export default App
