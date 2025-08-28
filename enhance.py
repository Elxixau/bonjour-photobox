import sys
import cv2
import numpy as np

if len(sys.argv) != 3:
    print("Usage: python enhance.py <input_path> <output_path>")
    sys.exit(1)

input_path = sys.argv[1]
output_path = sys.argv[2]

# --- Load image
img = cv2.imread(input_path)
if img is None:
    print("Error: image not found")
    sys.exit(1)

# --- Convert to float for processing
img_float = img.astype(np.float32) / 255.0

# --- Enhance contrast and brightness
alpha = 1.2  # contrast (1.0 = no change)
beta = 0.1   # brightness (0.0 = no change)
img_enhanced = np.clip(img_float * alpha + beta, 0, 1)

# --- Optional: sharpen
kernel = np.array([[0, -1, 0],
                   [-1, 5,-1],
                   [0, -1, 0]], dtype=np.float32)
img_enhanced = cv2.filter2D(img_enhanced, -1, kernel)

# --- Convert back to 8-bit
img_enhanced = (img_enhanced * 255).astype(np.uint8)

# --- Save
cv2.imwrite(output_path, img_enhanced)
print("Enhancement done:", output_path)
