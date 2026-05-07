from pathlib import Path
from typing import Dict, Any, List

import cv2
import mediapipe as mp
import numpy as np
from fastapi import FastAPI, UploadFile, File, HTTPException

app = FastAPI(title="Barbery Face Analysis AI") #tạo app FastAPI

BASE_DIR = Path(__file__).resolve().parent
MODEL_PATH = BASE_DIR / "models" / "face_landmarker.task" #tìm model

if not MODEL_PATH.exists():  #kiểm tra model
    raise RuntimeError(f"Thiếu model: {MODEL_PATH}")

#lấy class
BaseOptions = mp.tasks.BaseOptions
FaceLandmarker = mp.tasks.vision.FaceLandmarker
FaceLandmarkerOptions = mp.tasks.vision.FaceLandmarkerOptions
RunningMode = mp.tasks.vision.RunningMode

#cấu hình
options = FaceLandmarkerOptions(
    base_options=BaseOptions(model_asset_path=str(MODEL_PATH)),#đường dẫn
    running_mode=RunningMode.IMAGE, #sử lý ảnh tĩnh
    num_faces=1,  #chỉ phân tích 1 ảnh
)

landmarker = FaceLandmarker.create_from_options(options) #model chỉ load khi server khởi động

#danh sách kiểu tóc
STYLE_CATALOG = {
    "buzz-cut": {
        "name": "Buzz Cut",
        "image": "/images/hairstyles/buzz-cut.jpg",
        "description": "Rất gọn, mạnh mẽ, làm nổi bật đường nét góc cạnh.",
    },
    "crew-cut": {
        "name": "Crew Cut",
        "image": "/images/hairstyles/crew-cut.jpg",
        "description": "Thanh lịch, dễ chăm sóc và hợp nhiều dáng mặt nam tính.",
    },
    "french-crop": {
        "name": "French Crop",
        "image": "/images/hairstyles/french-crop.jpg",
        "description": "Hiện đại, gọn và giúp tổng thể gương mặt hài hòa hơn.",
    },
    "textured-crop": {
        "name": "Textured Crop",
        "image": "/images/hairstyles/textured-crop.jpg",
        "description": "Trẻ trung, gọn gàng, dễ tạo texture tự nhiên mỗi ngày.",
    },
    "ivy-league": {
        "name": "Ivy League",
        "image": "/images/hairstyles/ivy-league.jpg",
        "description": "Lịch sự, tinh tế, hợp phong cách công sở và học đường.",
    },
    "side-part": {
        "name": "Side Part",
        "image": "/images/hairstyles/side-part.jpg",
        "description": "Lịch lãm, dễ hợp nhiều gương mặt và nhiều hoàn cảnh.",
    },
    "slick-back": {
        "name": "Slick Back",
        "image": "/images/hairstyles/slick-back.jpg",
        "description": "Vuốt ngược gọn gàng, phù hợp gương mặt cân đối và rõ nét.",
    },
    "quiff": {
        "name": "Quiff",
        "image": "/images/hairstyles/quiff.jpg",
        "description": "Tạo độ phồng ở phần mái, giúp gương mặt thu hút hơn.",
    },
    "pompadour": {
        "name": "Pompadour",
        "image": "/images/hairstyles/pompadour.jpg",
        "description": "Kéo cao phần tóc trên, tạo cảm giác gương mặt dài hơn.",
    },
    "undercut": {
        "name": "Undercut",
        "image": "/images/hairstyles/undercut.jpg",
        "description": "Hiện đại, nam tính, hai bên gọn giúp tổng thể sáng mặt.",
    },
    "taper-fade": {
        "name": "Taper Fade",
        "image": "/images/hairstyles/taper-fade.jpg",
        "description": "Mềm mại, dễ hợp nhiều dáng mặt, không quá gắt.",
    },
    "skin-fade": {
        "name": "Skin Fade",
        "image": "/images/hairstyles/skin-fade.jpg",
        "description": "Rất gọn ở hai bên, làm nổi bật phần tóc phía trên.",
    },
    "messy-crop": {
        "name": "Messy Crop",
        "image": "/images/hairstyles/messy-crop.jpg",
        "description": "Tự nhiên, trẻ trung, hợp phong cách hiện đại.",
    },
    "curtain-hair": {
        "name": "Curtain Hair",
        "image": "/images/hairstyles/curtain-hair.jpg",
        "description": "Rẽ ngôi mềm mại, hợp phong cách Hàn và khuôn mặt thanh.",
    },
    "bro-flow": {
        "name": "Bro Flow",
        "image": "/images/hairstyles/bro-flow.jpg",
        "description": "Tóc buông tự nhiên, hợp mặt oval hoặc hơi dài.",
    },
}

FACE_SHAPE_LABELS = {
    "round": "Mặt tròn",
    "oval": "Mặt oval",
    "square": "Mặt vuông",
    "oblong": "Mặt dài",
}


# HELPERS

def clamp(x: float, min_value: float = 0.0, max_value: float = 1.0) -> float:  #ép giá trị từ 0 đến 1
    return max(min_value, min(max_value, x))


def safe_float(x: Any) -> float:  #chuyển dữ liệu sang số thực
    try:
        return float(x)
    except Exception:
        return 0.0


def distance(a: np.ndarray, b: np.ndarray) -> float:  #tính toán khoảng cách giữa 2 điểm
    return float(np.linalg.norm(a - b))


def to_np_points(face_landmarks) -> np.ndarray:
    return np.array([(lm.x, lm.y) for lm in face_landmarks], dtype=np.float32) #chỉ lấy tọa độ 2D


def cosine_similarity_score(value: float, target: float, tolerance: float) -> float: #tính tỉ lệ dáng mặt
    if tolerance <= 0:
        return 0.0
    diff = abs(value - target)
    score = 1.0 - (diff / tolerance)
    return clamp(score, 0.0, 1.0)


# ĐÁNH GIÁ CHẤT LƯỢNG ẢNH

def assess_quality(points: np.ndarray) -> Dict[str, Any]:
    # lấy các điểm trên mặt
    left_eye = points[33]
    right_eye = points[263]
    forehead = points[10]     #trán
    chin = points[152]        #cằm
    left_cheek = points[234]
    right_cheek = points[454]

    #chiều dài và độ rộng khuân mặt
    face_width = distance(left_cheek, right_cheek)
    face_height = distance(forehead, chin)

    # kiểm tra độ nghiêng mặt
    eye_dx = abs(right_eye[0] - left_eye[0]) + 1e-6
    eye_dy = abs(right_eye[1] - left_eye[1])
    eye_tilt_ratio = eye_dy / eye_dx
    frontal_score = 1.0 - min(eye_tilt_ratio * 2.2, 1.0)

    #xem mặt có ở giữ ko
    face_center_x = (left_cheek[0] + right_cheek[0]) / 2.0
    center_offset = abs(face_center_x - 0.5)
    center_score = 1.0 - min(center_offset * 2.6, 1.0)

    #mặt có đủ to ko
    size_score = clamp((face_width - 0.18) / 0.15, 0.0, 1.0)

    #mặt có méo ko
    ratio = face_height / (face_width + 1e-6)
    ratio_score = 1.0 - min(abs(ratio - 1.25) / 0.75, 1.0)

    #mặt có sát mép ảnh ko
    margin_left = left_cheek[0]
    margin_right = 1.0 - right_cheek[0]
    margin_top = forehead[1]
    margin_bottom = 1.0 - chin[1]
    margin_min = min(margin_left, margin_right, margin_top, margin_bottom)
    margin_score = clamp((margin_min - 0.02) / 0.06, 0.0, 1.0)

    #tính điểm tổng và so sánh với điểm hợp lý bên dưới
    quality_score = (
        frontal_score * 0.32
        + center_score * 0.20
        + size_score * 0.24
        + ratio_score * 0.12
        + margin_score * 0.12
    )

    #phân loại các kiểu mặt
    issues: List[str] = []
    if frontal_score < 0.55:
        issues.append("Ảnh hơi nghiêng, nên chụp chính diện hơn")
    if size_score < 0.45:
        issues.append("Khuôn mặt hơi xa, nên chụp cận hơn")
    if center_score < 0.55:
        issues.append("Khuôn mặt lệch khung hình, nên đưa mặt vào giữa")
    if margin_score < 0.45:
        issues.append("Khuôn mặt sát mép ảnh, nên chụp thoáng hơn")

    if quality_score < 0.45:
        level = "reject"
        message = "Ảnh chưa phù hợp để phân tích. Vui lòng chụp ảnh chính diện, rõ mặt, đủ sáng."
    elif quality_score < 0.70:
        level = "low"
        message = "Ảnh dùng được nhưng kết quả là ước lượng từ ảnh."
    else:
        level = "good"
        message = "Ảnh khá phù hợp để phân tích."

    return {
        "level": level,
        "score": round(float(quality_score), 4),
        "message": message,
        "issues": issues,
    }


# =========================================================
# PHÂN TÍCH 5 TIÊU CHÍ
# =========================================================
def classify_forehead_size(forehead_ratio: float) -> str: # tính tỉ lệ trán so với má
    if forehead_ratio >= 0.96:
        return "Rộng"
    if forehead_ratio >= 0.88:
        return "Trung bình"
    return "Hẹp"


def classify_jawline_shape(jaw_ratio: float, chin_taper_ratio: float) -> str:   #tính tỉ lệ mặt
    if jaw_ratio >= 0.90 and chin_taper_ratio <= 0.78:
        return "Vuông rõ"
    if jaw_ratio >= 0.82 and chin_taper_ratio <= 0.88:
        return "Cân đối"
    return "Thon"

# trích xuất chỉ số khuân mặt
def extract_metrics(points: np.ndarray) -> Dict[str, Any]:

    #đo chiều dài khuân mặt
    forehead_top = points[10]  #trán
    chin = points[152]         #cằm

    #đo chiều rộng khuân mặt
    left_cheek = points[234]
    right_cheek = points[454]

    #độ rộng hàm
    left_jaw = points[172]
    right_jaw = points[397]

    #độ rộng trán
    left_temple = points[54]
    right_temple = points[284]

    #độ rộng cằm
    left_lower_chin = points[149]
    right_lower_chin = points[378]


    face_height = distance(forehead_top, chin)              #chiều dài từ trán đến cằm.
    cheek_width = distance(left_cheek, right_cheek)         #chiều rộng vùng má
    jaw_width = distance(left_jaw, right_jaw)               #độ rộng phần hàm
    forehead_width = distance(left_temple, right_temple)    #độ rộng vùng trán
    chin_width = distance(left_lower_chin, right_lower_chin)#độ rộng phần cằm

    face_ratio = face_height / (cheek_width + 1e-6)         #tỉ lệ dài/rộng khuôn mặt
    jaw_ratio = jaw_width / (cheek_width + 1e-6)            #tỉ lệ hàm so với má
    forehead_ratio = forehead_width / (cheek_width + 1e-6)  #tỉ lệ trán so với má
    chin_taper_ratio = chin_width / (jaw_width + 1e-6)      #độ thon của cằm

    # 1. chiều dài mặt
    if face_ratio < 1.12:
        face_length = "Ngắn"
    elif face_ratio < 1.34:
        face_length = "Trung bình"
    else:
        face_length = "Dài"

    # 2. chiều rộng mặt
    if cheek_width > 0.34:
        face_width = "Rộng"
    elif cheek_width > 0.27:
        face_width = "Cân đối"
    else:
        face_width = "Hẹp"

    # 3. trán
    forehead_size = classify_forehead_size(forehead_ratio)

    # 4. hàm
    jawline_shape = classify_jawline_shape(jaw_ratio, chin_taper_ratio)

    return {
        "face_height": round(float(face_height), 4),
        "cheek_width": round(float(cheek_width), 4),
        "jaw_width": round(float(jaw_width), 4),
        "forehead_width": round(float(forehead_width), 4),
        "chin_width": round(float(chin_width), 4),
        "face_ratio": round(float(face_ratio), 4),
        "jaw_ratio": round(float(jaw_ratio), 4),
        "forehead_ratio": round(float(forehead_ratio), 4),
        "chin_taper_ratio": round(float(chin_taper_ratio), 4),
        "face_length": face_length,
        "face_width": face_width,
        "forehead_size": forehead_size,
        "jawline_shape": jawline_shape,
    }



# HÌNH DẠNG KHUÔN MẶT

def score_face_shapes(metrics: Dict[str, Any]) -> Dict[str, float]: # lấy dữ liệu đã được tính toán ép về số thực
    face_ratio = safe_float(metrics["face_ratio"])
    jaw_ratio = safe_float(metrics["jaw_ratio"])
    forehead_ratio = safe_float(metrics["forehead_ratio"])

    scores = {
        "round": (
            cosine_similarity_score(face_ratio, 1.02, 0.16) * 0.62
            + cosine_similarity_score(jaw_ratio, 0.90, 0.12) * 0.23
            + cosine_similarity_score(forehead_ratio, 0.93, 0.10) * 0.15
        ),
        "oval": (
            cosine_similarity_score(face_ratio, 1.24, 0.16) * 0.64
            + cosine_similarity_score(jaw_ratio, 0.81, 0.12) * 0.21
            + cosine_similarity_score(forehead_ratio, 0.94, 0.10) * 0.15
        ),
        "square": (
            cosine_similarity_score(face_ratio, 1.08, 0.15) * 0.48
            + cosine_similarity_score(jaw_ratio, 0.96, 0.10) * 0.34
            + cosine_similarity_score(forehead_ratio, 0.97, 0.10) * 0.18
        ),
        "oblong": (
            cosine_similarity_score(face_ratio, 1.42, 0.18) * 0.68
            + cosine_similarity_score(jaw_ratio, 0.79, 0.14) * 0.17
            + cosine_similarity_score(forehead_ratio, 0.92, 0.10) * 0.15
        ),
    }

    if face_ratio > 1.18:
        scores["round"] *= 0.82
    if face_ratio < 1.10:
        scores["oval"] *= 0.84
    if jaw_ratio > 0.92:
        scores["oval"] *= 0.88
    if face_ratio < 1.08 and metrics.get("face_width") == "Rộng":
        scores["round"] *= 1.08
    if face_ratio > 1.18 and jaw_ratio < 0.84:
        scores["oval"] *= 1.08

    for k in scores:
        scores[k] = round(clamp(scores[k], 0.0, 1.0) * 100, 2)

    return scores

#chọn dáng mặt phù hợp nhất
def pick_face_shape(shape_scores: Dict[str, float]) -> Dict[str, Any]:
    ordered = sorted(shape_scores.items(), key=lambda x: x[1], reverse=True) #sắp xếp theo điểm top 1 và 2
    best_shape, best_score = ordered[0]
    second_shape, second_score = ordered[1]

    delta = best_score - second_score #khoảng cách điểm giữa top 1 và 2
    uncertain = delta < 8

    #nếu chưa chắc chắn
    if uncertain:
        display_label = f"{FACE_SHAPE_LABELS.get(best_shape, best_shape)} thiên {FACE_SHAPE_LABELS.get(second_shape, second_shape).replace('Mặt ', '').lower()}"
    else:
        display_label = FACE_SHAPE_LABELS.get(best_shape, best_shape)

    #độ tin cậy
    confidence = clamp(best_score / 100.0, 0.0, 1.0)

    return {
        "face_shape": best_shape,
        "face_shape_label": display_label,
        "confidence": round(confidence, 4),
        "uncertain": uncertain,
        "top_shapes": [
            {"key": k, "label": FACE_SHAPE_LABELS.get(k, k), "score": s}
            for k, s in ordered[:3]
        ],
        "second_shape": second_shape,
        "second_score": second_score,
    }



# CHẤM ĐIỂM KIỂU TÓC THEO 5 TIÊU CHÍ

def top_hairstyles(face_shape: str, second_shape: str, confidence: float, metrics: Dict[str, Any]) -> List[Dict[str, Any]]:
    scores = {key: 0 for key in STYLE_CATALOG.keys()}

    # điểm bắt đầu là 0 và cộng điểm theo 5 tiêu chí
    face_length = metrics.get("face_length")
    face_width = metrics.get("face_width")
    forehead = metrics.get("forehead_size")
    jaw = metrics.get("jawline_shape")

    # 1. Hình dạng khuôn mặt
    shape_map = {
        "round": ["quiff", "pompadour", "side-part", "undercut", "slick-back", "taper-fade"],
        "oval": ["textured-crop", "side-part", "undercut", "messy-crop", "ivy-league", "bro-flow"],
        "square": ["crew-cut", "buzz-cut", "taper-fade", "ivy-league", "skin-fade", "side-part"],
        "oblong": ["french-crop", "curtain-hair", "messy-crop", "textured-crop", "crew-cut", "bro-flow"],
    }

    for i, s in enumerate(shape_map.get(face_shape, [])):
        scores[s] += max(20 - i * 3, 5)

    for i, s in enumerate(shape_map.get(second_shape, [])):
        scores[s] += max(10 - i * 2, 3)

    # 2. Chiều dài khuôn mặt
    if face_length == "Ngắn":
        scores["quiff"] += 10
        scores["pompadour"] += 10
        scores["slick-back"] += 6
        scores["undercut"] += 4
        scores["skin-fade"] += 4

    elif face_length == "Trung bình":
        scores["textured-crop"] += 5
        scores["side-part"] += 5
        scores["ivy-league"] += 4
        scores["messy-crop"] += 4
        scores["taper-fade"] += 4

    elif face_length == "Dài":
        scores["french-crop"] += 10
        scores["crew-cut"] += 7
        scores["messy-crop"] += 5
        scores["curtain-hair"] += 5
        scores["pompadour"] -= 5
        scores["quiff"] -= 3

    # 3. Chiều rộng khuôn mặt
    if face_width == "Rộng":
        scores["side-part"] += 8
        scores["undercut"] += 8
        scores["taper-fade"] += 6
        scores["quiff"] += 4
        scores["skin-fade"] += 4
        scores["buzz-cut"] -= 5

    elif face_width == "Cân đối":
        scores["textured-crop"] += 4
        scores["crew-cut"] += 4
        scores["ivy-league"] += 4
        scores["messy-crop"] += 4
        scores["bro-flow"] += 3

    elif face_width == "Hẹp":
        scores["textured-crop"] += 6
        scores["curtain-hair"] += 6
        scores["bro-flow"] += 5
        scores["messy-crop"] += 4

    # 4. Kích thước trán
    if forehead == "Rộng":
        scores["french-crop"] += 8
        scores["messy-crop"] += 6
        scores["curtain-hair"] += 5
        scores["quiff"] -= 3
        scores["pompadour"] -= 4
        scores["slick-back"] -= 5

    elif forehead == "Trung bình":
        scores["side-part"] += 4
        scores["ivy-league"] += 4
        scores["textured-crop"] += 3
        scores["taper-fade"] += 3

    elif forehead == "Hẹp":
        scores["quiff"] += 8
        scores["pompadour"] += 8
        scores["slick-back"] += 6
        scores["bro-flow"] += 4
        scores["curtain-hair"] -= 2

    # 5. Đường hàm
    if jaw == "Vuông rõ":
        scores["crew-cut"] += 6
        scores["buzz-cut"] += 6
        scores["ivy-league"] += 4
        scores["skin-fade"] += 5
        scores["side-part"] += 4

    elif jaw == "Cân đối":
        scores["undercut"] += 5
        scores["side-part"] += 5
        scores["quiff"] += 5
        scores["taper-fade"] += 4
        scores["textured-crop"] += 4

    elif jaw == "Thon":
        scores["undercut"] += 6
        scores["slick-back"] += 5
        scores["bro-flow"] += 5
        scores["curtain-hair"] += 4
        scores["quiff"] += 4

    # nếu confidence thấp thì ưu tiên kiểu an toàn
    if confidence < 0.65:
        scores["textured-crop"] += 8
        scores["side-part"] += 8
        scores["ivy-league"] += 6
        scores["crew-cut"] += 5

    # chia nhóm để top 6 đa dạng hơn
    style_groups = {
        "bro-flow": "long",
        "curtain-hair": "long",

        "buzz-cut": "very_short",
        "crew-cut": "very_short",

        "french-crop": "crop",
        "messy-crop": "crop",
        "textured-crop": "crop",

        "pompadour": "volume",
        "quiff": "volume",

        "side-part": "classic",
        "slick-back": "classic",
        "ivy-league": "classic",

        "skin-fade": "fade",
        "taper-fade": "fade",
        "undercut": "fade",
    }
    #sắp xếp kiểu tóc theo điểm
    ordered_raw = sorted(scores.items(), key=lambda x: x[1], reverse=True)

    selected = [] #danh sách kiểu tóc được chọn
    used_groups = {} #đếm số kiểu tóc được chọn

    #duyệt từng kiểu tóc theo điểm
    for key, score in ordered_raw:
        group = style_groups.get(key, key)
        group_count = used_groups.get(group, 0)

        if group_count >= 2:
            continue

        selected.append((key, score))
        used_groups[group] = group_count + 1

        if len(selected) == 6:
            break

    suggestions = []
    for key, score in selected:
        item = STYLE_CATALOG[key].copy()
        item["key"] = key
        item["score"] = score
        suggestions.append(item)

    return suggestions


#trả kết quả khi ko phân tích được ảnh

def fallback_response(message: str = "Chưa thể phân tích ổn định từ ảnh hiện tại.") -> Dict[str, Any]:
    return {
        "status": "need_better_photo",
        "message": message,
        "face_shape": "unknown",
        "face_shape_label": "Chưa xác định ổn định",
        "confidence": 0.0,
        "analysis_summary": None,
        "metrics": None,
        "top_shapes": [],
        "suggestions": [],
        "quality": {
            "level": "reject",
            "score": 0.0,
            "message": message,
            "issues": [],
        },
    }


# API

@app.get("/")
def home():
    return {"ok": True, "service": "barbery-ai"} #test server

#nhận file ảnh
@app.post("/analyze-face")
async def analyze_face(image: UploadFile = File(...)):

    #kiểm tra file
    if not image:
        raise HTTPException(status_code=400, detail="Thiếu file ảnh")
    contents = await image.read()
    if not contents:
        raise HTTPException(status_code=400, detail="File ảnh rỗng")

    #giải mã file/bytes thành ma trận pixel
    np_img = np.frombuffer(contents, np.uint8)
    img = cv2.imdecode(np_img, cv2.IMREAD_COLOR)

    if img is None:
        raise HTTPException(status_code=400, detail="Ảnh không hợp lệ")

    #chạy mediapipe
    rgb = cv2.cvtColor(img, cv2.COLOR_BGR2RGB)
    mp_image = mp.Image(image_format=mp.ImageFormat.SRGB, data=rgb)
    result = landmarker.detect(mp_image)

    if not result.face_landmarks:
        return fallback_response("Không phát hiện được khuôn mặt. Vui lòng dùng ảnh rõ mặt, chính diện hơn.")

    points = to_np_points(result.face_landmarks[0])


    quality = assess_quality(points)
    if quality["level"] == "reject":
        return {
            **fallback_response(quality["message"]),
            "quality": quality,
        }

    metrics = extract_metrics(points)
    shape_scores = score_face_shapes(metrics)
    picked = pick_face_shape(shape_scores)

    suggestions = top_hairstyles(
        face_shape=picked["face_shape"],
        second_shape=picked["second_shape"],
        confidence=picked["confidence"],
        metrics=metrics,
    )

    analysis_summary = (
        f"Hệ thống ước lượng khuôn mặt của bạn là {picked['face_shape_label'].lower()}. "
        f"Chiều dài khuôn mặt: {metrics['face_length'].lower()}, "
        f"chiều rộng khuôn mặt: {metrics['face_width'].lower()}, "
        f"kích thước trán: {metrics['forehead_size'].lower()}, "
        f"đường hàm: {metrics['jawline_shape'].lower()}. "
        f"5 tiêu chí này được dùng để đề xuất kiểu tóc phù hợp."
    )

    status = "ok" if quality["level"] == "good" else "low_quality_result"

    return {
        "status": status,
        "message": quality["message"],
        "face_shape": picked["face_shape"],
        "face_shape_label": picked["face_shape_label"],
        "confidence": picked["confidence"],
        "analysis_summary": analysis_summary,
        "metrics": metrics,
        "top_shapes": picked["top_shapes"],
        "uncertain": picked["uncertain"],
        "quality": quality,
        "suggestions": suggestions,
    }
