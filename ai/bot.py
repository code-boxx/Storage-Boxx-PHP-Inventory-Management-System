# (A) LOAD REQUIRED MODULES
import os, json, asyncio, websockets
from langchain.vectorstores import Chroma
from langchain.embeddings import GPT4AllEmbeddings
from langchain.llms import GPT4All
from langchain.chains import RetrievalQA
from langchain import PromptTemplate

# (B) SETTINGS & CHECKS
# (B1) PATH
set = { "path" : os.path.dirname(os.path.realpath(__file__)) }
set["path_db"] = os.path.join(set["path"], "db")

# (B2) LOAD JSON SETTINGS
with open(os.path.join(set["path"], "settings.json"), "r") as fs:
  set.update(json.load(fs))
  fs.close()
set["model_file"] = os.path.join(set["path"], "models", set["model_file"])

# (B3) CHECK - DB VECTOR STORE
if not os.path.exists(set["path_db"]):
  print("Database does not exist - " + set["path_db"])
  exit()

# (B4) LOAD PROMPT TEMPLATE
with open(os.path.join(set["path"], "prompt.txt"), "r") as fs:
  set["prompt"] = fs.read()
  fs.close()

# (C) INIT - DB, LLM, PROMPT TEMPLATE
db = Chroma(
  persist_directory = set["path_db"],
  embedding_function = GPT4AllEmbeddings()
)
llm = GPT4All(
  model = set["model_file"],
  max_tokens = set["model_tokens"]
)
prompt = PromptTemplate(
  template = set["prompt"], input_variables = ["context", "question"]
)

# (D) Q&A SESSION
# (D1) LET THE BOT SPEAK!
async def qna (websocket):
  async for query in websocket:
    qa = RetrievalQA.from_chain_type(
      llm = llm,
      chain_type = set["chain_type"],
      retriever = db.as_retriever(search_kwargs = {"k": set["chain_kwargs"]}),
      chain_type_kwargs = { "prompt": prompt },
      return_source_documents = set["chain_source"]==1
    )
    answer = qa(query)
    if type(answer) is dict:
      print(answer) # more for debugging
      answer = answer["result"]
    await websocket.send(answer)

# (D2) GO!
async def main ():
  async with websockets.serve(qna, set["ws_host"], set["ws_port"]):
    print("Server deployed at " + set["ws_host"] + ":" + str(set["ws_port"]))
    await asyncio.Future()

if __name__ == "__main__":
  asyncio.run(main())