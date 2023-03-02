from fastapi import FastAPI
from pydantic import BaseModel
from starlette.middleware.cors import CORSMiddleware

import gravis as gv
import hashlib

app = FastAPI()

@app.get("/")
async def root():
    return {"message": "Hello, World!"}

class RequestModel(BaseModel):
    graph: str


class ResponseModel(BaseModel):
    # This is the schema of the expected response and depends on what you
    # return from get_data.
    result: str

# app = FastAPI()
# app.add_middleware(CORSMiddleware, allow_origins=["*"])

@app.post("/vis", summary="Get vis graph html", response_model=ResponseModel)
def vis(query: RequestModel):
    """Process a batch of articles and return the entities predicted by the
    given model. Each record in the data should have a key "text".
    """
    print(query.graph);
    fig = gv.vis(query.graph, show_node_label=False, show_edge_label=True, edge_label_data_source='en')
    md5 = hashlib.md5(query.graph.encode())
    filename = md5.hexdigest() + '.html'
    filepath = '/workspace/files/' +  filename
    # fig.export_html(filename)
    fig.export_html(filepath, overwrite=True)
    return {"result": filename}

origins=["*"]
app = CORSMiddleware(
    app=app,
    allow_origins=origins,
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)