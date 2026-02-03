from fastapi import FastAPI
from pydantic import BaseModel
from transformers import AutoTokenizer, AutoModelForSequenceClassification
import torch
from dotenv import load_dotenv
import os

# Load env
load_dotenv()
MODEL_NAME = os.getenv("HF_MODEL", "unitary/unbiased-toxic-roberta")
THRESHOLD = float(os.getenv("THRESHOLD", 0.5))

# Load model
tokenizer = AutoTokenizer.from_pretrained(MODEL_NAME)
model = AutoModelForSequenceClassification.from_pretrained(MODEL_NAME)

app = FastAPI(title="Review Moderation Service")

class ReviewInput(BaseModel):
    text: str

@app.post("/check")
def check_review(data: ReviewInput):
    inputs = tokenizer(data.text, return_tensors="pt", truncation=True)
    with torch.no_grad():
        outputs = model(**inputs)
        probs = torch.softmax(outputs.logits, dim=1)[0]
        toxic_prob = probs[1].item()  # assuming label 1 = toxic

    flagged = toxic_prob >= THRESHOLD

    return {
        "flagged": flagged,
        "score": toxic_prob
    }
