import pandas as pd
import mysql.connector
import surprise
from surprise import Dataset, Reader
import joblib
import os

# Configuración de la base de datos MySQL
db_config = {
    'user': 'root',                # Usuario de MySQL
    'password': '',                # Contraseña de MySQL
    'host': '127.0.0.1',           # Dirección del servidor MySQL (localhost)
    'database': 'donlito'          # Nombre de la base de datos
}

# Ruta para guardar el modelo en la ubicación deseada (sin la fecha actual)
model_path = 'C:/xampp/htdocs/Don-Lito-S.A/api/modelos/modelo_recomendacion.pkl'

def obtener_datos_de_base_de_datos():
    try:
        # Conexión a la base de datos
        conn = mysql.connector.connect(**db_config)
        
        if conn.is_connected():
            cursor = conn.cursor(dictionary=True)
            
            # Obtener los datos de pedidos y detalles de pedido
            cursor.execute("""
                SELECT pd.producto_id, pd.cantidad, p.usuario_id
                FROM pedidos p
                JOIN pedido_detalles pd ON p.id = pd.pedido_id
            """)
            # Cargar los datos en un DataFrame de pandas
            data = cursor.fetchall()
            df = pd.DataFrame(data)
            
            return df

    except mysql.connector.Error as err:
        print(f"Error al obtener datos de la base de datos: {err}")
        return None

    finally:
        if 'cursor' in locals():
            cursor.close()
        if 'conn' in locals() and conn.is_connected():
            conn.close()

def entrenar_y_guardar_modelo():
    # Obtener los datos
    df = obtener_datos_de_base_de_datos()
    
    if df is None or df.empty:
        print("No se encontraron datos para entrenar el modelo.")
        return
    
    print("Datos obtenidos para entrenamiento:")
    print(df.head())  # Ver los primeros registros para verificar la estructura

    # Inicializar el Reader para el Dataset de Surprise
    reader = Reader(rating_scale=(1, 5))  # Ajusta el rango según tus necesidades (1-5 en este caso)

    # Crear el dataset de Surprise a partir del DataFrame
    data = Dataset.load_from_df(df[['usuario_id', 'producto_id', 'cantidad']], reader)

    # Entrenar el modelo utilizando SVD (algoritmo de matriz de factorización)
    trainset = data.build_full_trainset()
    model = surprise.SVD()  # Puedes cambiar el algoritmo aquí si lo deseas
    model.fit(trainset)

    # Guardar el modelo entrenado en el archivo con el mismo nombre
    joblib.dump(model, model_path)
    print(f"Modelo entrenado y guardado exitosamente en: {model_path}")

    return model, df[['usuario_id', 'producto_id']].drop_duplicates()

def generar_recomendaciones(model, df_usuarios_productos):
    # Generar recomendaciones para cada usuario
    recomendaciones = {}
    
    for _, usuario_producto in df_usuarios_productos.iterrows():
        usuario_id = usuario_producto['usuario_id']
        producto_id = usuario_producto['producto_id']
        
        # Predecir la cantidad (o "calificación") que el usuario podría dar a este producto
        prediccion = model.predict(usuario_id, producto_id)
        
        if usuario_id not in recomendaciones:
            recomendaciones[usuario_id] = []
        
        recomendaciones[usuario_id].append((producto_id, prediccion.est))
    
    # Ordenar las recomendaciones para cada usuario por la predicción (de mayor a menor)
    for usuario_id in recomendaciones:
        recomendaciones[usuario_id].sort(key=lambda x: x[1], reverse=True)
    
    return recomendaciones

def mostrar_recomendaciones(recomendaciones):
    # Mostrar las recomendaciones de productos para cada usuario
    for usuario_id, productos in recomendaciones.items():
        print(f"\nRecomendaciones para el Usuario {usuario_id}:")
        for producto_id, score in productos[:10]:  # Mostrar los 10 productos más recomendados
            print(f"  Producto ID: {producto_id}, Predicción de compra: {score:.2f}")

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
            
            # Cargar los datos en un DataFrame de pandas
            data = cursor.fetchall()
            if data:
                df_productos = pd.DataFrame(data)
                return df_productos
            else:
                print("Consulta no devolvió resultados.")
                return pd.DataFrame()  # Devolver un DataFrame vacío en lugar de None
    except mysql.connector.Error as err:
        print(f"Error al obtener datos de la base de datos: {err}")
        return pd.DataFrame()  # Devolver un DataFrame vacío en caso de error
    finally:
        if 'cursor' in locals():
            cursor.close()
        if 'conn' in locals() and conn.is_connected():
            conn.close()

def mostrar_productos_mas_vendidos(df):
    if df is not None and not df.empty:
        print("\nProductos más vendidos:")
        for _, row in df.iterrows():
            print(f"  Producto ID: {row['producto_id']}, Total vendido: {row['total_vendido']}")
    else:
        print("No se encontraron productos vendidos.")

# Entrenar el modelo y generar recomendaciones
try:
    modelo, df_usuarios_productos = entrenar_y_guardar_modelo()
    if modelo:
        recomendaciones = generar_recomendaciones(modelo, df_usuarios_productos)
        mostrar_recomendaciones(recomendaciones)
        # Mostrar los productos más vendidos
        df_productos = obtener_productos_mas_vendidos()
        mostrar_productos_mas_vendidos(df_productos)
except Exception as e:
    print(f"Error al entrenar y guardar el modelo: {e}")




