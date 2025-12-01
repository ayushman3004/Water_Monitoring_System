import cv2
import numpy as np
import matplotlib.pyplot as plt

def predict_pollution_from_color(img_path):
    """
    Predicts water pollution based on image color analysis.
    
    Args:
        img_path (str): Path to the water image
        
    Returns:
        tuple: (label, pollution_percentage)
    """
    # Read and resize image
    img = cv2.imread(img_path)
    if img is None:
        raise ValueError(f"Could not read image at {img_path}")
        
    img = cv2.resize(img, (128, 128))

    # Calculate average color
    avg_color = img.mean(axis=0).mean(axis=0)  # BGR
    blue, green, red = avg_color

    # Simple heuristic rules for pollution detection
    if red > 100 and green < 100 and blue < 100:
        label = "Polluted Water"
        pollution_pct = 90
    elif green > 130 and red < 100:
        label = "Algae-rich (Possibly Polluted)"
        pollution_pct = 75
    elif blue > red and blue > green:
        label = "Clean Water"
        pollution_pct = 10
    else:
        label = "Uncertain - Possibly Polluted"
        pollution_pct = 50

    print(f"🧪 Predicted: {label} ({pollution_pct:.2f}% polluted)")
    return label, pollution_pct

def visualize_color_analysis(img_path):
    """
    Visualizes the color analysis of the water image.
    
    Args:
        img_path (str): Path to the water image
    """
    img = cv2.imread(img_path)
    if img is None:
        raise ValueError(f"Could not read image at {img_path}")
        
    img = cv2.resize(img, (128, 128))
    
    # Convert BGR to RGB for plotting
    img_rgb = cv2.cvtColor(img, cv2.COLOR_BGR2RGB)
    
    # Calculate color histograms
    color = ('r', 'g', 'b')
    plt.figure(figsize=(12, 4))
    
    plt.subplot(121)
    plt.imshow(img_rgb)
    plt.title('Input Image')
    plt.axis('off')
    
    plt.subplot(122)
    for i, col in enumerate(color):
        hist = cv2.calcHist([img], [i], None, [256], [0, 256])
        plt.plot(hist, color=col)
    plt.title('Color Distribution')
    plt.xlabel('Pixel Intensity')
    plt.ylabel('Count')
    plt.grid(True, alpha=0.3)
    
    plt.tight_layout()
    plt.show()

if __name__ == "__main__":
    # Example usage
    test_image = "path_to_your_water_image.jpg"
    try:
        label, pollution = predict_pollution_from_color(test_image)
        visualize_color_analysis(test_image)
    except Exception as e:
        print(f"Error: {str(e)}") 