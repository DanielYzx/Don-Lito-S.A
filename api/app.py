from flask import Flask, jsonify
import joblib
import mysql.connector
from mysql.connector import Error
import pandas as pd
from apscheduler.schedulers.background import BackgroundScheduler

# Crear la instancia de la aplicación Flask
app = Flask(__name__)

# Configuración de la base de datos MySQL
db_config = {
    'user': 'root',                # Usuario de MySQL
    'password': '',                # Contraseña de MySQL
    'host': '127.0.0.1',           # Dirección del servidor MySQL (localhost)
    'database': 'donlito'          # Nombre de la base de datos
}

# Ruta al modelo entrenado
MODEL_PATH = 'C:/xampp/htdocs/Don-Lito-S.A/api/modelos/modelo_recomendacion.pkl'

# Cargar el modelo de recomendación
model = None

def cargar_modelo():
    global model
    try:
        model = joblib.load(MODEL_PATH)
        print("Modelo cargado exitosamente.")
    except Exception as e:
        print(f"Error al cargar el modelo: {e}")
    print("Modelo actualizado a las", pd.to_datetime("now"))


# Inicializar el modelo al arrancar la API
cargar_modelo()

# Configuración del programador para actualizar el modelo cada 5 minutos
scheduler = BackgroundScheduler()
scheduler.add_job(func=cargar_modelo, trigger="interval", minutes=30)
scheduler.start()

def obtener_datos_de_base_de_datos():
    try:
        conn = mysql.connector.connect(**db_config)
        if conn.is_connected():
            cursor = conn.cursor(dictionary=True)
            cursor.execute("""
                SELECT pd.producto_id, pd.cantidad, p.usuario_id
                FROM pedidos p
                JOIN pedido_detalles pd ON p.id = pd.pedido_id
            """)
            data = cursor.fetchall()
            return pd.DataFrame(data)
    except Error as err:
        print(f"Error al obtener datos: {err}")
        return None
    finally:
        if 'cursor' in locals():
            cursor.close()
        if 'conn' in locals() and conn.is_connected():
            conn.close()

@app.route('/recomendaciones/<int:usuario_id>', methods=['GET'])
def get_recommendations(usuario_id):
    try:
        # Obtener los datos de la base de datos
        df = obtener_datos_de_base_de_datos()
        if df is None or df.empty:
            return jsonify({'mensaje': 'No se encontraron datos para generar recomendaciones.'}), 404

        # Validar si el modelo está cargado
        if not model:
            return jsonify({'error': 'El modelo no está disponible.'}), 500

        # Obtener los productos únicos y generar predicciones
        productos = df['producto_id'].unique()
        recomendaciones = [
            {
                'producto_id': int(producto_id),  # Convertir a tipo estándar
                'score': float(model.predict(usuario_id, producto_id).est)  # Convertir a tipo estándar
            }
            for producto_id in productos
        ]

        # Ordenar por puntuación descendente
        recomendaciones.sort(key=lambda x: x['score'], reverse=True)

        # Retornar las 5 mejores recomendaciones
        return jsonify({'usuario_id': usuario_id, 'recomendaciones': recomendaciones[:6]})
    except Exception as e:
        return jsonify({'error': str(e)}), 500

def obtener_productos_mas_vendidos():
    try:
        # Conexión a la base de datos
        conn = mysql.connector.connect(**db_config)
        
        if conn.is_connected():
            cursor = conn.cursor(dictionary=True)
            
            # Consulta SQL para obtener los productos más vendidos
            query = """
                SELECT pd.producto_id, SUM(pd.cantidad) AS total_vendido
                FROM pedido_detalles pd
                JOIN pedidos p ON pd.pedido_id = p.id
                GROUP BY pd.producto_id
                ORDER BY total_vendido DESC
            """
            cursor.execute(query)
            
            # Obtener los resultados como una lista de diccionarios
            data = cursor.fetchall()
            return data

    except mysql.connector.Error as err:
        print(f"Error al obtener datos de la base de datos: {err}")
        return None

    finally:
        if 'cursor' in locals():
            cursor.close()
        if 'conn' in locals() and conn.is_connected():
            conn.close()

@app.route('/productos-mas-vendidos', methods=['GET'])
def get_top_products():
    try:
        # Obtener productos más vendidos desde la base de datos
        productos = obtener_productos_mas_vendidos()
        
        # Comprobar si no hay productos
        if not productos:  # Verifica si la lista está vacía
            return jsonify({'mensaje': 'No se encontraron productos más vendidos.'}), 404

        # Convertir los datos a tipos estándar para JSON
        productos_serializables = [
            {
                'producto_id': int(row['producto_id']),   # Convertir a int
                'total_vendido': int(row['total_vendido'])  # Convertir a int
            }
            for row in productos  # Iterar directamente sobre la lista de diccionarios
        ]

        return jsonify(productos_serializables)
    except Exception as e:
        return jsonify({'error': str(e)}), 500

# Iniciar la API Flask
if __name__ == '__main__':
    app.run(debug=True)





