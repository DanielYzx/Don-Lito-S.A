from flask import Flask, jsonify
import joblib
import pandas as pd

# Crear la instancia de la aplicación Flask
app = Flask(__name__)

# Ruta a los archivos de datos y modelos
MODEL_PATH = 'api/modelos/modelo_recomendacion.pkl'  # Cambia la ruta si es necesario
PRODUCTOS_PATH = 'api/datos/productos.csv'
PEDIDOS_PATH = 'api/datos/pedidos.csv'
PEDIDO_DETALLES_PATH = 'api/datos/pedido_detalles.csv'

# Cargar el modelo y los datos
model = joblib.load(MODEL_PATH)  # Ruta al modelo entrenado
productos = pd.read_csv(PRODUCTOS_PATH)
pedidos = pd.read_csv(PEDIDOS_PATH)
pedido_detalles = pd.read_csv(PEDIDO_DETALLES_PATH)

# Ruta para obtener recomendaciones
# Ruta para obtener recomendaciones
@app.route('/recomendaciones/<int:usuario_id>', methods=['GET'])
def get_recommendations(usuario_id):
    # Filtrar los pedidos del usuario
    pedidos_usuario = pedidos[pedidos['usuario_id'] == usuario_id]

    # Verificar si el usuario tiene pedidos
    if pedidos_usuario.empty:
        return jsonify({'usuario_id': usuario_id, 'recomendaciones': [], 'mensaje': 'El usuario no tiene pedidos previos.'})

    # Obtener los productos comprados a través de 'pedido_detalles'
    pedidos_ids = pedidos_usuario['id'].unique()  # IDs de los pedidos del usuario
    productos_comprados = pedido_detalles[pedido_detalles['pedido_id'].isin(pedidos_ids)]['producto_id'].unique()

    # Obtener todos los productos disponibles
    product_ids = productos['id'].unique()

    # Filtrar productos no comprados
    productos_no_comprados = [prod for prod in product_ids if prod not in productos_comprados]

    # Predecir las puntuaciones para los productos no comprados
    scores = [(product, model.predict(usuario_id, product).est) for product in productos_no_comprados]

    # Ordenar productos por puntuación predicha
    top_scores = sorted(scores, key=lambda x: x[1], reverse=True)[:5]  # Top 5 productos

    # Preparar la respuesta
    recomendaciones = [productos[productos['id'] == product]['nombre'].values[0] for product, score in top_scores]

    return jsonify({'usuario_id': usuario_id, 'recomendaciones': recomendaciones})
# Iniciar el servidor Flask
if __name__ == '__main__':
    app.run(debug=True)
